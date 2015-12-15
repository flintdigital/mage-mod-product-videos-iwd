;if(typeof(jQueryIWD) == "undefined"){if(typeof(jQuery) != "undefined") {jQueryIWD = jQuery;}} $ji = jQueryIWD;
window.hasOwnProperty = function (obj) {return (this[obj]) ? true : false;};
if (!window.hasOwnProperty('IWD')) {IWD = {};}
IWD = IWD||{};
IWD.PV = IWD.PV||{};

IWD.PV.Admin = {
    LOCAL: 'local',
    YOUTUBE: 'youtube',
    VIMEO: 'vimeo',
    updateVideoPositionUrl: '',
    imageNote: {
        local: "Upload thumbnail from you PC.</br>Supported formats: jpg, jpeg, png, gif",
        youtube: "Thumbnail will upload from YouTube, but you can upload custom thumbnail.</br>Supported formats: jpg, jpeg, png, gif",
        vimeo: "Thumbnail will upload from Vimeo, but you can upload custom thumbnail.</br>Supported formats: jpg, jpeg, png, gif"
    },
    youtubeAPIKey: null,
    youtubeWarning: 'To use this button, fill Youtube API key in Extension Configuration',

    init: function () {
        IWD.PV.Admin.initBase();

        IWD.PV.Admin.autoFillButtonAdd();
        IWD.PV.Admin.videoTypeOption();

        $ji('#video_type').on("change", function () {
            IWD.PV.Admin.videoTypeOption();
        });

        $ji("#iwd_productvideo").on("mouseenter", function () {
            if (!IWD.PV.Admin.eventsList($ji(".sortable_video"))){
                IWD.PV.Admin.sort();
            }
            IWD.PV.Admin.playVideo();
        });

        IWD.PV.Admin.attachProducts();
    },

    initBase: function(){
        IWD.PV.children = IWD.PV.Admin;
        IWD.PV.urlGetVideo = IWD.PV.Admin.urlGetVideo;
        IWD.PV.inPopup = 1;
    },

    playVideo: function(){
        $ji(".play-button").off();
        $ji('.play-button').on('click touchstart', function () {
            $ji(this).closest('tr').click();
            var video_id = $ji(this).data('video-id');
            IWD.PV.loadVideo(video_id);
        });

        $ji('.pv-iwd-modal').on('hide.bs.modal', function () {
            IWD.PV.closeAllVideos();
        });
    },

    localVideoPlayerInit: function () {
        if ($ji('.local-video-player').length > 0) {
            videojs(document.getElementsByClassName('local-video-player')[0], {}, function () {});
        }
    },

    eventsList: function (element) {
        //for different version $ji
        if ($ji._data(element[0], 'events') !== undefined) return true;
        if (element.data('events') !== undefined) return true;
        if ($ji.data(element, 'events') !== undefined) return true;
        if ($ji._data(element, 'events') !== undefined) return true;
        return false;
    },

    sort: function () {
        $ji(".sortable_video").sortable({
            containment: "parent",
            update: function (event, ui) {
                var sortableArray = $ji(this).sortable("toArray");
                var videoIDs = sortableArray.map(function (e) {
                    return e.split("_").first();
                });
                var productID = sortableArray.first().split('_').last();

                IWD.PV.Admin.showLoadingMask();

                $ji.ajax({
                    url: IWD.PV.Admin.updateVideoPositionUrl,
                    type: "POST",
                    dataType: 'json',
                    data: 'video_ids=' + videoIDs + '&product_id=' + productID + '&form_key=' + FORM_KEY,
                    success: function (result) {
                        IWD.PV.Admin.addMessage('Sorted position of the video was successfully changed', 'success');
                    },
                    error: function (result) {
                        IWD.PV.Admin.addMessage('Sorted position of the video was not changed', 'error');
                    }
                });
            }
        });
    },

    addMessage: function (message, type) {
        $ji('#loading-mask').hide();

        $ji("#messages")
            .append('<ul class="messages"><li class="' + type + '-msg">' + message + '</li></ul>')
            .show()
            .delay(2000)
            .hide(1000);

        setTimeout(function () {
            $ji('#messages').empty();
        }, 3000);
    },

    showLoadingMask: function () {
        var width = $ji("html").width();
        var height = $ji("html").height();
        $ji('#loading-mask').width(width).height(height).css('top', 0).css('left', -2).show();
    },

    videoTypeOption: function () {
        IWD.PV.Admin.hideOptions();
        IWD.PV.Admin.showOption();
        IWD.PV.Admin.autoFillButtonShow($ji('#video_type').val());
    },

    hideOptions: function () {
        var options = $ji.map($ji("#video_type option"), function (ele) {
            return ele.value;
        });

        $ji.each(options, function (index, option) {
            var opt = $ji('#' + option + '_url');
            opt.removeClass('required-entry');
            opt.parents('tr').hide();
            opt.attr('name', option + '_url');
        });
    },

    showOption: function () {
        var value = $ji('#video_type').val();
        var selectedValue = $ji('#' + value + '_url');
        selectedValue.parents('tr').show();
        selectedValue.attr('name', 'url');
        selectedValue.addClass('required-entry');
        $ji('#image').removeClass('required-entry');
        $ji('label[for="image"] span[class="required"]').hide();


        if ($ji("#video_id").length != 0 && value == IWD.PV.Admin.LOCAL) {
            $ji('label[for="local_url"] span[class="required"]').hide();
            selectedValue.removeClass('required-entry');
        }
        if ($ji("#video_id").length == 0 && value == IWD.PV.Admin.LOCAL) {
            $ji('label[for="image"] span[class="required"]').css('display', 'inline-block');
            $ji('#image').addClass('required-entry');
        }

        $ji("#image_note").html(IWD.PV.Admin.imageNote[value]);
    },

    getYoutubeVideoId: function(url) {
        if(typeof IWD.PV.Admin.youtubeParseUrl != 'undefined') {
            var regex = IWD.PV.Admin.youtubeParseUrl;
            var results = url.match(regex);
            if(results === null)
                return url;
            if(typeof results[1] != 'undefined')
                return results[1];
        }
        return url;
    },

    getVimeoVideoId: function(url) {
        if(typeof IWD.PV.Admin.vimeoParseUrl != 'undefined') {
            var regex = IWD.PV.Admin.vimeoParseUrl;
            var results = url.match(regex);
            if(results === null)
                return url;
            if(typeof results[5] != 'undefined')
                return results[5];
        }
        return url;
    },

    autoFillButtonAdd: function () {
        $ji("#title").parents("tr").append('<td id="upload_info"><span class="form-button '+(!IWD.PV.Admin.youtubeAPIKey ? 'disabled' : '')+'" title="'+(!IWD.PV.Admin.youtubeAPIKey ? IWD.PV.Admin.youtubeWarning : '')+'">Autofill Information</span></td>');
        $ji('#upload_info').on("click", function () {
            var span = jQueryIWD(this).children('span.disabled');
            if(span.length) {
                alert(span.attr('title'));
                return;
            }
            IWD.PV.Admin.showLoadingMask();

            switch ($ji('#video_type').val()) {
                case IWD.PV.Admin.LOCAL:
                    break;

                case IWD.PV.Admin.YOUTUBE:
                    var url = $ji('#youtube_url').val();
                    url = IWD.PV.Admin.getYoutubeVideoId(url);
                    IWD.PV.Admin.getYouTubeInfo(url);
                    break;

                case IWD.PV.Admin.VIMEO:
                    var url = $ji('#vimeo_url').val();
                    url = IWD.PV.Admin.getVimeoVideoId(url);
                    console.log(url);
                    IWD.PV.Admin.getVimeoInfo(url);
                    break;
            }
            $ji('#loading-mask').hide();
        });
    },

    autoFillButtonShow: function (type) {
        $ji("#upload_info").hide();

        if(type == IWD.PV.Admin.YOUTUBE && !IWD.PV.Admin.youtubeAPIKey) {
            jQueryIWD('#upload_info .form-button').addClass('disabled').attr('title', IWD.PV.Admin.youtubeWarning);
        } else {
            jQueryIWD('#upload_info .form-button').removeClass('disabled').attr('title', '');
        }

        switch (type) {
            case IWD.PV.Admin.YOUTUBE:
            case IWD.PV.Admin.VIMEO:
                $ji("#upload_info").show();
                break;
        }
    },

    getYouTubeInfo: function (code) {
        if (code == "") {
            $ji('#youtube_url').focus().addClass("error_input");
            return;
        }

        try {
            $ji.ajax({
                type: 'get',
                url: "https://www.googleapis.com/youtube/v3/videos",
                data: {
                    part:   'snippet',
                    key:    IWD.PV.Admin.youtubeAPIKey,
                    id:     code
                },
                dataType: "json",
                success: function (data) {
                    try {
                        $ji("#title").val(data.items[0].snippet.title);
                        $ji("#description").val(data.items[0].snippet.description);
                    }
                    catch(e) {
                        alert('Impossible to autofill video information');
                    }
                },
                error: function(data) {
                    if(typeof data.responseJSON.error.errors != 'undefined') {
                        alert('message: ' + data.responseJSON.error.errors[0].message + '\nreason: ' + data.responseJSON.error.errors[0].reason);
                    }
                }
            });
        } catch (er) {
            console.log("ERROR");
        }
    },

    getVimeoInfo: function (code) {
        if (code == "") {
            $ji('#vimeo_url').focus().addClass("error_input");
            return;
        }

        $ji.ajax({
            type: 'GET',
            url:  window.location.protocol + '//vimeo.com/api/v2/video/' + code + '.json',
            jsonp: 'callback',
            dataType: 'jsonp',
            success: function (data) {
                var video = data[0];
                $ji("#title").val(video.title);
                $ji("#description").val(video.description);
            }
        });
    },

    attachProducts: function() {
        $ji(document).on('change', '[type="checkbox"].ajax-product-attach', function(e){
            e.preventDefault();
            IWD.PV.Admin.attach(this);
        });
    },
    attach: function(elem) {
        var _this = $ji(elem);
        var id = _this.val();
        var loaderId = 'loader-' + id;
        _this.wrap('<i class="fa fa-circle-o-notch fa-spin" id="' + loaderId + '"></i>');
        _this.css('display', 'none');
        var checked = _this.prop('checked') ? 1 : 0;
        var videoId = $ji('[name="video_id"]').val();

        var data = {
            form_key: FORM_KEY,
            product_id: id,
            video_id: videoId,
            attach: checked
        };

        $ji.post(_this.data('url'), data, function(response) {
            $ji('#' + loaderId).find('input').each(function() {
                $ji(this).css('display', 'inline-block');
                $ji(this).unwrap();
            });
            if (typeof(response.error) != "undefined") {
                $ji('#messages').html('<ul class="messages"><li class="error-msg"><ul><li>' + response.error + '</li></ul></li></ul>');
            }
        },'json');
    },

    rowClick: function (grid, event) {
        if(event.target.tagName.toLowerCase() != 'input') {
            var row = Event.findElement(event, 'tr');
            IWD.PV.Admin.attach(jQueryIWD(row).find('[type="checkbox"]'));
        }
    }
};
