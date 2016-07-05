;if(typeof(jQueryIWD) == "undefined"){if(typeof(jQuery) != "undefined") {jQueryIWD = jQuery;}} $ji = jQueryIWD;
window.hasOwnProperty = function (obj) {return (this[obj]) ? true : false;};
if (!window.hasOwnProperty('IWD')) {IWD = {};}
IWD = IWD||{};
IWD.PV = IWD.PV||{};
IWD.PV.Frontend = {
    flashPlayer: "/js/iwd/productvideo/video-js.swf",
    inPopup: 0,

    imageBox: "",
    thumbnailsBox: "",
    thumbnailsPosition: 'after',
    videoIdAsFirstImage: 0,
    thumbnail: {},
    class_thumb_image: "iwd-pv-thumb-image",
    class_thumb_video: "iwd-pv-thumb-video",
    class_video_template: ".iwd-pv-video",
    class_video: "",
    fixedThumbnailsSize: false,
    fake_block: 'iwd-pv-fake-thumb-block',

    init: function () {
        IWD.PV.Frontend.class_video = IWD.PV.Frontend.imageBox + " " + IWD.PV.Frontend.class_video_template;

        IWD.PV.Frontend.initBase();

        IWD.PV.Frontend.insertVideoBox();
        IWD.PV.Frontend.insertThumbnailsBox();
        IWD.PV.Frontend.insertPopupBox();
        IWD.PV.Frontend.initThumbnails();

        IWD.PV.Frontend.setVideoAsFirstImage();
    },

    initBase: function(){
        IWD.PV.children = IWD.PV.Frontend;
        IWD.PV.urlGetVideo = IWD.PV.Frontend.urlGetVideo;
        IWD.PV.inPopup = IWD.PV.Frontend.inPopup;
    },

    insertVideoBox: function(){
        $ji(IWD.PV.Frontend.imageBox).find('.iwd-pv-video').remove();
        var video_box = $ji('#iwd-pv-video-box').html();
        //$ji(IWD.PV.Frontend.imageBox).append(video_box); //UNCOMMENT THIS LINE IF WITH A THEME OTHER THAN ZEBRA.
        $ji(IWD.PV.Frontend.thumbnailsBoxAfter).append(video_box); //COMMENT THIS LINE IF WITH A THEME OTHER THAN ZEBRA.
        $ji('#iwd-pv-video-box').empty();
    },

    insertThumbnailsBox: function(){
        $ji(IWD.PV.Frontend.thumbnailsBox).each(function(id, box){
            if($ji(box) != null && $ji(box).length > 0){
                IWD.PV.Frontend.prepareThumbnails(box);
//                IWD.PV.Frontend.prepareThumbnailsInfo(box);
            }

//            var video_box = IWD.PV.Frontend.prepareThumbnails(box);
//            IWD.PV.Frontend.insertVideoThumbnails(box, video_box);
        });
    },

//    insertVideoThumbnails: function(box, video_box){
//        if(IWD.PV.Frontend.thumbnailsPosition == 'after'){
//            $ji(box).append(video_box);
//        } else {
//            $ji(box).prepend(video_box);
//        }
//    },


    insertPopupBox: function(){
        $ji('body > #iwd_productvideo_popup').remove();
        var video_box = $ji('#iwd-pv-popup-box').html();
        $ji('body').append(video_box);
        $ji('body #iwd-pv-popup-box .pv-iwd-modal').remove();

        $ji('.pv-iwd-modal').on('hide.bs.modal', function () {
            IWD.PV.closeAllVideos();
        });
    },

//    prepareThumbnails: function(box) {
//        var html = "";
//
//        $ji('#iwd-pv-thumbnails-box').find('.iwd-pv-item').each(function(){
//            $ji(this).find('img').css('width', IWD.PV.Frontend.thumbnail.width).css('height', IWD.PV.Frontend.thumbnail.height).html();
//            html += '<' + IWD.PV.Frontend.thumbnail.tag + ' class="' + IWD.PV.Frontend.class_thumb_video + '">' + $ji(this).html() + '</' + IWD.PV.Frontend.thumbnail.tag + '>';
//        });
//
//        return html;
//    },

    setVideoAsFirstImage: function(){
        if(IWD.PV.Frontend.videoIdAsFirstImage != "0") {
            // compatibility with QV-2
            if($ji(IWD.PV.Frontend.class_video).closest('.iwd-qv-modal').length) {
                setTimeout(function(){
                    if(typeof ProductMediaManagerQV != 'undefined')
                        ProductMediaManagerQV.destroyZoom();
                    IWD.PV.loadVideo(IWD.PV.Frontend.videoIdAsFirstImage);
                }, 500);
            } else {
                IWD.PV.loadVideo(IWD.PV.Frontend.videoIdAsFirstImage);
            }
        }
    },

    prepareThumbnails: function(box){
        var children = $ji(box).children();
        if($ji(children) != null && $ji(children).length > 0) {
            $ji.each(children, function(k, v) {
                var elem = $ji(v);
                var src = elem.find('img').eq(0).attr('src');
                if(typeof src == 'undefined') {
                    src = elem.find('img').eq(0).data('src');
                }
                if(src.indexOf('iwd_video') > 0) {
                    var path = src.split('/');
                    var file = path[path.length - 1];
                    elem.attr('data-video-id', file);
                    elem.addClass(IWD.PV.Frontend.class_thumb_video);
                    elem.prepend($ji('<i>').attr('class', 'iwd-pv-icon-play fa fa-1x fa-play'));
                } else {
                    elem.addClass(IWD.PV.Frontend.class_thumb_image);
                }
            });
            var fake = $ji('<div>').addClass(IWD.PV.Frontend.fake_block);
            children.append(fake);
        }
    },

//    prepareThumbnailsInfo: function(box){
//        var children = $ji(box).children();
//        if($ji(children) != null && $ji(children).length > 0){
//            IWD.PV.Frontend.thumbnail.tag = children[0].tagName.toLowerCase();
//            var img = $ji(children[0]).find('img')[0];
//            var width = $ji(img).width();
//            var height = $ji(img).height();
//            /* if in sys conf - do not use fixed size - detect it automatically */
//            if(!IWD.PV.Frontend.fixedThumbnailsSize) {
//                IWD.PV.Frontend.thumbnail.width = width;
//                IWD.PV.Frontend.thumbnail.height = height;
//            }
//        }
//    },

    initThumbnails: function(){
        $ji(document).on('click touchstart', '.' + IWD.PV.Frontend.fake_block, function(e){
            var wrap = $ji(this).parent();
            if(wrap.hasClass(IWD.PV.Frontend.class_thumb_video)) {
                e.preventDefault();
                if(wrap.attr('data-video-id')) {
                    IWD.PV.Frontend.initThumbnailsVideo(wrap.attr('data-video-id'));
                }
            } else {
                wrap.find('img').trigger('click');
                IWD.PV.Frontend.initThumbnailsImage();
            }
        } );
    },

    initThumbnailsImage: function(){
        $ji(IWD.PV.Frontend.class_video).hide();
        IWD.PV.closeAllVideos();
    },

    initThumbnailsVideo: function(videoId){
        IWD.PV.loadVideo(videoId);
    },

    loadPlayerToImageBlock: function (result) {
        $ji(IWD.PV.Frontend.class_video).show();
        $ji(IWD.PV.Frontend.class_video + " .iwd-pv-video-block").html(result.embed_code);
    },

    deletePlayerFromImageBlock: function () {
        $ji(IWD.PV.Frontend.class_video).hide();
        $ji(IWD.PV.Frontend.class_video + " .iwd-pv-video-block").html("");
    },

    preLoaderHideImageBlock: function(){
        $ji(IWD.PV.Frontend.class_video).show();
        $ji(IWD.PV.Frontend.class_video + " .iwd-pv-video-preloader-wrapper").hide();
        $ji(IWD.PV.Frontend.class_video + " .iwd-pv-video-block").show();
    },

    preLoaderShowImageBlock: function(){
        $ji(IWD.PV.Frontend.class_video).show();
        $ji(IWD.PV.Frontend.class_video + " .iwd-pv-video-preloader-wrapper").show();
        $ji(IWD.PV.Frontend.class_video + " .iwd-pv-video-block").hide();
    }
};
