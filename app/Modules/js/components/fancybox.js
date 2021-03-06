document.addEventListener('DOMContentLoaded', function() {

    var fancybox = $('.fancybox')
    //var fancybox = $('[data-fancybox]')

    if (fancybox.length) {

        fancybox.fancybox({
            lang: 'ru',
            i18n: {
                'ru': {
                    CLOSE: 'Закрыть',
                    NEXT: 'Следующий',
                    PREV: 'Предыдущий',
                    ERROR: 'Запрошенное содержимое не может быть загружено. <br>Пожалуйста, повторите попытку позже.',
                    PLAY_START: 'Запуск слайд-шоу',
                    PLAY_STOP: 'Остановить слайд-шоу',
                    FULL_SCREEN: 'На весь экран',
                    THUMBS: 'Миниатюры',
                    DOWNLOAD: 'Скачать',
                    SHARE: 'Поделиться',
                    ZOOM: 'Приблизить'
                }
            }
        })
    }

}, false)
