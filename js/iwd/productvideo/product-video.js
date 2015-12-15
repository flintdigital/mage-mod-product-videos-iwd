;
if (typeof(jQueryIWD) == "undefined") {
    if (typeof(jQuery) != "undefined") {
        jQueryIWD = jQuery;
    }
}
$ji = jQueryIWD;
window.hasOwnProperty = function (obj) {
    return (this[obj]) ? true : false;
};
if (!window.hasOwnProperty('IWD')) {
    IWD = {};
}
IWD = IWD || {};

IWD.PV = {
    uploadedVideos: {},
    urlGetVideo: "",
    inPopup: 1,
    current_video: null,
    loading: false,
    children: null,


    loadVideo: function (video_id) {
//        if (!$ji.isNumeric(video_id)){
//            return;
//        }

        IWD.PV.current_video = video_id;

        if (IWD.PV.uploadedVideos[video_id]) {
            if (IWD.PV.uploadedVideos[video_id] !== 'loading') {
                IWD.PV.preLoaderShow();
                IWD.PV.loadPlayerTo(IWD.PV.uploadedVideos[video_id]);
                return;
            }
            if (IWD.PV.uploadedVideos[video_id] === 'loading') {
                return;
            }
        }

        IWD.PV.uploadedVideos[video_id] = 'loading';
        IWD.PV.preLoaderShow();

        $ji.ajax({
            url: IWD.PV.urlGetVideo,
            type: "POST",
            dataType: 'json',
            data: "video_id=" + video_id,
            success: function (result) {
                if (result.status == 1) {
                    IWD.PV.uploadedVideos[video_id] = result;
                    IWD.PV.loadPlayerTo(result);
                }
            },
            error: function () {
                IWD.PV.uploadedVideos[video_id] = null;
                IWD.PV.loadPlayerTo(null);
            }
        });
    },

    preLoaderShow: function () {
        if (IWD.PV.inPopup == 1) {
            IWD.PV.preLoaderShowPopupBlock();
        } else {
            IWD.PV.children.preLoaderShowImageBlock();
        }
    },

    preLoaderHide: function () {
        if (IWD.PV.inPopup == 1) {
            IWD.PV.preLoaderHidePopupBlock();
        } else {
            IWD.PV.children.preLoaderHideImageBlock();
        }
    },

    loadPlayerTo: function (result) {
        if (IWD.PV.loading == false && IWD.PV.current_video != null && result != null) {
            if (IWD.PV.inPopup == 1) {
                IWD.PV.loadPlayerToPopupBlock(result);
            } else {
                IWD.PV.children.loadPlayerToImageBlock(result);
            }
        }

        IWD.PV.localVideoPlayerInit();
        IWD.PV.preLoaderHide();
    },

    localVideoPlayerInit: function () {
        if ($ji('.local-video-player').length > 0) {
            videojs(document.getElementsByClassName('local-video-player')[0], {}, function () {
            });
        }
    },

    closeAllVideos: function () {
        IWD.PV.deletePlayerFromPopupBlock();
        if (IWD.PV.children != null) {
            IWD.PV.children.deletePlayerFromImageBlock();
        }
        IWD.PV.current_video = null;
    },

    loadPlayerToPopupBlock: function (result) {
        $ji('.pv-iwd-modal').modaliwd('hide');
        $ji(".pv-iwd-modal-title").html(result.title);
        $ji('.pv-iwd-modal-body .iwd-pv-video-block').html(result.embed_code).promise().done(function () {
            $ji('.pv-iwd-modal-body .iwd-pv-video-description').html(result.description).promise().done(function () {
                var options = {"show": true};
                $ji('.iwd-pv-video-preloader-wrapper').hide();
                $ji('.pv-iwd-modal').modaliwd();
            });
        });
    },

    deletePlayerFromPopupBlock: function () {
        $ji(".pv-iwd-modal-title").html("");
        $ji('.pv-iwd-modal-body .iwd-pv-video-block').html("");
        $ji('.pv-iwd-modal-body .iwd-pv-video-description').html("");
        $ji('.pv-iwd-modal').hide();
    },

    preLoaderHidePopupBlock: function () {
        $ji('.pv-iwd-modal-content .iwd-pv-video-preloader-wrapper').hide();
    },

    preLoaderShowPopupBlock: function () {
        var options = {"show": true};
        $ji('.pv-iwd-modal').modaliwd(options);
        $ji('.pv-iwd-modal').data('bs.modal').handleUpdate();
        $ji('.pv-iwd-modal-content .iwd-pv-video-preloader-wrapper').show();
    }
};