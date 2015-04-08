$(function() {
    var count;
    var url = $("link[rel='canonical']").attr('href');

    var facebook_api = 'http://graph.facebook.com/?id=' + encodeURI(url);
    $.getJSON(facebook_api + '&callback=?', function(json){
        count = 0;
        if(json.shares){ count = json.shares; }
        $('.facebook-like-count').text(count);
    });

    var hatena_api = 'http://api.b.st-hatena.com/entry.count?url=' + encodeURI(url);
    $.getJSON(hatena_api + '&callback=?', function(json){
        count = 0;
        if(json){ count = json; }
        $('.hatena-bookmark-count').text(count);
    });
    
    var twitter_api = 'http://cdn.api.twitter.com/1/urls/count.json?url=' + encodeURI(url);
    $.getJSON(twitter_api + '&callback=?', function(json){
        count = 0;
        if(json.count){ count = json.count; }
        $('.twitter-tweet-count').text(count);
    });

});
