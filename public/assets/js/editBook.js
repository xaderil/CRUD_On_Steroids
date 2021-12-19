$('#editBook').on('show.bs.modal', function (event) {

    let bookID = $(event.relatedTarget).data('bookId')
    $(this).find('.inputTitle').val(bookID)

})