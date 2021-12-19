
var template = '<div class="input-group help-block"><input type="text" name = "authors[]" class="form-control" placeholder="Автор"/></div>';
var minusButton = '<div class="input-group-append delete-field"><span class="btn input-group-text delete-field"><i class="fa fa-minus-square"></i></span></div>';

$('.add-fieldEdit').click(function (node, child) {
    var temp = $(template);
    temp.append(minusButton);
    $('.help-block').last().append(temp);
});

$(document).on('click', '.delete-field', function(){
    $(this).parents('.input-group').first().remove();
});
