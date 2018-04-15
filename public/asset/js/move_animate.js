function moveAnimate(element, newParent) {
    //Allow passing in either a jQuery object or selector
    element = $(element);
    newParent = $(newParent);

    var oldOffset = element.offset();
    element.appendTo(newParent);
    var newOffset = element.offset();

    var temp = element.clone().appendTo('body');
    temp.css({
        'position': 'absolute',
        'left': oldOffset.left,
        'top': oldOffset.top,
        'z-index': 1000
    });
    element.hide();
    temp.animate({'top': newOffset.top, 'left': newOffset.left}, 'slow', function () {
        element.show();
        temp.remove();
    });
}