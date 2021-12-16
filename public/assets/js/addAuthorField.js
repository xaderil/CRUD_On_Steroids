
var template = '<div class=\"input-group\"><input type=\"text\" class=\"form-control\" placeholder="Автор"/></div>';
// var minusButton = '<span class="btn input-group-addon delete-field">(-)</span>';
var minusButton = '<div class="input-group-append delete-field"><span class="btn input-group-text delete-field"><i class="fa fa-minus-square"></i></span></div>';
$('.add-field').click(function (node, child) {
    var temp = $(template).insertBefore('.help-block', child);
    temp.append(minusButton);
});

$(document).on('click', '.delete-field', function(){
    $(this).parents('.input-group').first().remove();
});
