/**
 * tinymce编辑器初始化（页面需自行引入tinymce库）
 */
function loadTinymceEditor(cls, uploadType, options){
    if(typeof cls == 'undefined') return;
    if(typeof uploadType == 'undefined') uploadType = 'default';

    let toolbar = [
        'undo redo | restoredraft | alignleft aligncenter alignright alignjustify |  outdent indent | ltr rtl',
        'code | link | image | media  | blockquote | forecolor backcolor',
    ].join(' | ');

    let initParams = {
        selector: '.'+cls,
        language:'zh_CN',
        height: 600,
        menubar: false,
        statusbar: false,
        plugins: 'image, media, code, link, autosave',
        toolbar: toolbar,
        mobile: {
            toolbar_drawer: false
        },

        // image
        images_upload_credentials: true,
        images_upload_url : '/space/upload/file?upload_type=' + uploadType,
        image_dimensions:false,
        images_upload_handler: function (blobInfo, success, failure) {
            let file = blobInfo.blob();

            //上传前限制尺寸，避免上传图片过大
            imageResize(file).then(function(newFile){
                file = newFile;

                // 获取文件MD5
                getFileMD5(file).then(function(newFileName){
                    //获取上传签名
                    $.ajax({
                        type: 'get',
                        url: '/common/upload/sign',
                        data: {
                            upload_type: uploadType,
                        },
                        success: function(res){
                            let sign = JSON.parse(res.data.sign);
                            let key = sign.dir + '/' + newFileName;

                            //判断文件大小是否超出
                            if(sign.size < file.size){
                                failure('文件大小超出' + (sign.size/1024/1024) + 'MB限制');
                                return;
                            }

                            let formData = new FormData();
                            formData.append('OSSAccessKeyId', sign.accessid);
                            formData.append('policy', sign.policy);
                            formData.append('Signature', sign.signature);
                            formData.append('key', key);
                            formData.append('success_action_status', 200);
                            formData.append('file', file, file.name);

                            //服务端返回签名后，上传到oss
                            $.ajax({
                                url: bucketUrl,
                                type:'post',
                                data: formData,
                                processData: false,
                                contentType: false,
                                error: function(){
                                    failure('上传失败');
                                },
                                success:function(res){
                                    if(res.state == 0){
                                        failure(res.msg);
                                    } else{
                                        success(bucketUrl + '/' + key);

                                        //上传成功后标记到缓存
                                        $.get('/common/upload/oss_uploaded', {
                                            source: bucketUrl+'/'+key
                                        });
                                    }
                                },
                            });
                        }
                    });
                });
            });
        },

        // media
        media_alt_source:false,
        media_poster:false,
        media_url_resolver: function (data, resolve, reject) {
            // 视频地址解析
            var src = data.url.replace('http://','').replace('https://','');
            if( data.url.indexOf('.guitarworld.com.') !== -1 ){
                src = 'http://' + src;
            } else if( data.url.indexOf('v.youku.com') !== -1 ){
                src = src.replace(/v\.youku\.com\/v_show\/id_([\w\-=]+)\.html/i, 'https://player.youku.com/embed/$1');
            } else if( data.url.indexOf('v.qq.com') !== -1 ){
                src = src.replace(/v\.qq\.com\/.*\/(.*)?\.html/i, 'https://v.qq.com/iframe/player.html?vid=$1&tiny=0&auto=0');
            } else if( data.url.indexOf('www.bilibili.com') !== -1 ){
                if(data.url.indexOf('video/BV') !== -1 || data.url.indexOf('video/bv') !== -1){
                    src = src.replace(/www\.bilibili\.com\/.*\/bv([^\?]*)/i, '//player.bilibili.com/player.html?danmaku=0&bvid=$1&');
                } else{
                    src = src.replace(/www\.bilibili\.com\/.*\/av(\d+)\??/i, '//player.bilibili.com/player.html?danmaku=0&aid=$1&');
                }
            } else {
                resolve({html: ''});
            }

            resolve({html: '<iframe allowfullscreen="true" src="'+src+'" frameborder="0" scrolling="no" border="0" framespacing="0"></iframe>'});
        }
    };

    tinymce.init($.extend(initParams, options));
}

/**
 * 图片尺寸调整（目前用于上传前初步压缩）
 */
function imageResize(file){
    const MAX_WIDTH = 1920;
    const MAX_HEIGHT = 1920;

    return new Promise(function (resolve, reject){
        // Load the image
        let reader = new FileReader();
        reader.onload = function (readerEvent) {
            let image = new Image();
            image.onload = function (imageEvent) {
                // Resize the image
                let canvas = document.createElement('canvas'),
                    width = image.width,
                    height = image.height;
                if (width > height) {
                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }
                } else {
                    if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }
                }
                canvas.width = width;
                canvas.height = height;
                canvas.getContext('2d').drawImage(image, 0, 0, width, height);

                let dataUrl = canvas.toDataURL(file.type);
                // let resizedImage = dataURLToBlob(dataUrl);
                let resizedImage = (function(dataUrl){
                    let BASE64_MARKER = ';base64,';
                    if (dataUrl.indexOf(BASE64_MARKER) === -1) {
                        let parts = dataURL.split(',');
                        let contentType = parts[0].split(':')[1];
                        let raw = parts[1];

                        return new Blob([raw], {type: contentType});
                    }

                    var parts = dataUrl.split(BASE64_MARKER);
                    var contentType = parts[0].split(':')[1];
                    var raw = window.atob(parts[1]);
                    var rawLength = raw.length;
                    var uInt8Array = new Uint8Array(rawLength);

                    for (var i = 0; i < rawLength; ++i) {
                        uInt8Array[i] = raw.charCodeAt(i);
                    }

                    return new Blob([uInt8Array], {type: contentType});
                })(dataUrl);
                resizedImage.name = file.name;

                resolve(resizedImage);
            }
            image.src = readerEvent.target.result;
        }
        reader.readAsDataURL(file);
    });
}

/**
 * 获取文件MD5（页面需自行引入spark-md5库）
 */
function getFileMD5(file){
    return new Promise(function(resolve, reject){
        let fileReader = new FileReader();
        fileReader.onload = function (e) { //FileReader的load事件，当文件读取完毕时触发
            // e.target指向上面的fileReader实例
            if (file.size !== e.target.result.length) { //如果两者不一致说明读取出错
                reject();
                return false;
            } else {
                resolve(SparkMD5.hashBinary(e.target.result) + '.' + file.name.split('.').pop());
            }
        };
        fileReader.onerror = function () { //如果读取文件出错
            reject();
        };
        fileReader.readAsBinaryString(file);
    });
}

function getUrlParams(){
    var queryString = window.location.search.substring(1);
    var paramArr = queryString == '' ? [] : queryString.split('&');
    var params = {};
    for(var i=0; i<paramArr.length; i++){
        var pair = paramArr[i].split("=");
        params[pair[0]] = pair[1];
    }
    return params;
}
