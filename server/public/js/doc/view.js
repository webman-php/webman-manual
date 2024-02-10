$(document).on('click', '.sidebar-toggle-button', function () {
    $(document.body).toggleClass('closed');
});

function request(p,b){
    var u = this.href || p;
    if (!b && u == window.location.href) {
        return;
    }
    $.ajax({
        url: u,
        success: function(n){
            !b && history.pushState({
                path: u
            }, null, u);
            var l = $(n);
            if(/<title>(?<title>.*)<\/title>/.test(n)){
                document.title = RegExp.$1;
            }
            var m = $(".sidebar").scrollTop();
            $('#main').replaceWith(l.find("#main"));
            $('#lang').replaceWith(l.find("#lang"));
            $('#repo').replaceWith(l.find("#repo"));
            $('.sidebar-nav').replaceWith(l.find(".sidebar-nav"));
            $('.sidebar').scrollTop(m);
            init();
        }
    });
    return false;
}

function scroll_to_hash()
{
    var hash = location.hash.slice(1);
    if (!hash) return;
    var el = $('*[data-id="' + hash + '"]');
    if (!el.length) return;
    var top = el.parent().position().top;
    var diff = $(window).height() - top;
    diff = diff > 60 ? 60 : (diff < 0 ? 0 : diff);
    top = top > $(window).height() ? top + 60: top - diff + 60;
    console.log(top);
    $(window).scrollTop(top);
}

$(document).on("click",'.sidebar a', request);

function init() {
    $.each($('.sidebar .sidebar-nav a'), function(k,a){
        if (location.protocol+'//'+location.host+location.pathname == a.href) {
            $(a).parent().addClass('active');
        }else{
            $(a).parent().removeClass('active');
        }
    });

    $(".content pre").addClass("prettyprint");
    prettyPrint();
    if (!location.hash.slice(1)) {
        $(document).scrollTop(0);
    } else {
        scroll_to_hash();
    }
    var cb = function (k, v) {
        var content = $(v).html();
        $(v).html('<a href="#' + encodeURIComponent(content) + '" data-id="' + encodeURIComponent(content) + '">' + content + '</a>');
    };
    $.each($('h1'), cb);
    $.each($('h2'), cb);
    $.each($('h3'), cb);
}
init();
window.onpopstate = function(e) {
    if (null !== e.state) return request(e.state.path, 1);
};

try {
    $('.sidebar').scrollTop($('.active').position().top - $(window).height() / 2 + 30);
}catch (e) {}

function ajax_search() {
    if (ajax_search.lock) return;
    var keyword = $('#keyword').val();
    if (keyword == '') {
        $('#search-result').empty();
        return;
    }
    ajax_search.lock = 1;
    $.ajax({
        url: '/search/doc',
        data: {keyword: keyword, name: doc_name, step:200},
        success: function(e){
            ajax_search.lock = 0;
            $('#search-result').empty();
            var keyword_now = $('#keyword').val();
            if (keyword_now != '') {
                $('#search-result').append(e);
            }
            if (keyword_now != keyword) {
                ajax_search();
            }
        },
        error: function () {
            ajax_search.lock = 0;
        }
    });
}

$('input[name="keyword"]').on('keyup', ajax_search);
$('input[name="keyword"]').on('click', ajax_search);

setTimeout(scroll_to_hash, 10);
$(window).on('hashchange', scroll_to_hash);