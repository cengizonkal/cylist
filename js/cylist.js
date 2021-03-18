function loadMoreVideos(token) {
    jQuery.post(cylist_ajax_obj.ajax_url, {
        _ajax_nonce: cylist_ajax_obj.nonce,
        action: "my_tag_count",
        token: token
    }, function (data) {
        console.log(data)
    });
}
