
document.addEventListener('DOMContentLoaded', function() {

    // id модального окна
    const modalId = 'cart_modal'

    // Проверяем подключен ли jQuery
    if (window.jQuery) {


        // Если есть класс .no_js, то отключаем JS
        if (!$('div').hasClass('no_js')) {


            // Показать корзину по клику на .cart_show
            $('.cart_show').on('click', function (e) {
                e.preventDefault()

                $.ajax({
                    type: 'GET',
                    url: '/cart/show',
                    success: function (res) {
                        showCart(res, modalId)
                    },
                    error: function () {
                        alert(translations['something_went_wrong'])
                    }
                })
            })



            // Добавить товар в корзину по клику на .cart_plus
            $(document).on('click', '.cart_plus', function (e) {
                e.preventDefault()

                const $this = $(this),
                    id = $this.data('id')

                if (id) {
                    $.ajax({
                        type: 'GET',
                        url: '/cart/' + id + '/plus',
                        //data: {id: id},
                        success: function (res) {

                            // Товар не найден
                            if (!res) {
                                alert(translations['something_went_wrong'])
                            }

                            showCart(res, modalId)
                        },
                        error: function () {
                            alert(translations['something_went_wrong'])
                        }
                    })
                }
            })



            // Отминусовать товар из корзины по клику на .cart_minus
            $(document).on('click', '.cart_minus', function (e) {
                e.preventDefault()

                const $this = $(this),
                    id = $this.data('id')

                if (id) {
                    $.ajax({
                        type: 'GET',
                        url: '/cart/' + id + '/minus',
                        //data: {id: id},
                        success: function (res) {

                            // Товар не найден
                            if (!res) {
                                alert(translations['something_went_wrong'])
                            }

                            showCart(res, modalId)
                        },
                        error: function () {
                            alert(translations['something_went_wrong'])
                        }
                    })
                }
            })



            // Удалить товар из корзину по клику на .cart_destroy
            $(document).on('click', '.cart_destroy', function (e) {
                e.preventDefault()

                const $this = $(this),
                    id = $this.data('id')

                if (id) {
                    $.ajax({
                        type: 'GET',
                        url: '/cart/' + id + '/destroy',
                        success: function (res) {

                            // Товар не найден
                            if (!res) {
                                alert(translations['something_went_wrong'])
                            }

                            showCart(res, modalId)
                        },
                        error: function () {
                            alert(translations['something_went_wrong'])
                        }
                    })
                }
            })

        }



        // Функция показа корзины, принимает содержимое корзины, в ответе на ajax
        function showCart(cart, modalId) {
            var modalInstance = new BSN.Modal('#' + modalId)

            // Вставим в модальное окно содержимое корзины
            $('#' + modalId + ' .modal-body').html(cart)

            // Открыть модальное окно
            modalInstance.show()


            var cartQty = $('#cart_modal_qty').text(),
                cartSum = $('#cart_modal_sum').text()

            // Вставляем кол-во из корзины в кнопку вызова
            //$('.cart_count_qty').text(cartQty)

            // Вставляем сумму из корзины в кнопку вызова
            $('.cart_count_sum').text(cartSum ? cartSum + ' ₽' : '')
        }
    }


}, false)
