document.addEventListener('DOMContentLoaded', function () {
    // Открытие модального окна
    document.querySelectorAll('.formDogBlock').forEach(button => {
        button.addEventListener('click', function () {
            let modalId = this.getAttribute('data-id');
            document.getElementById(modalId).style.display = 'flex';
        });
    });

    // Закрытие модального окна
    document.querySelectorAll('.close').forEach(closeButton => {
        closeButton.addEventListener('click', function () {
            let modalId = this.getAttribute('data-id');
            document.getElementById(modalId).style.display = 'none';
        });
    });

    // Закрытие окна при клике вне формы
    window.addEventListener('click', function (event) {
        document.querySelectorAll('.modal').forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Обработчик отправки формы через AJAX
    document.querySelectorAll('.form__item').forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // предотвращаем обычную отправку формы

            let actionField = form.querySelector('#action');
            if (!actionField || !actionField.value) {
                console.error('Ошибка: нет параметра action');
                alert('Произошла ошибка: отсутствует параметр действия');
                return;
            }

            let formData = new FormData(form); // создаем объект FormData

            // Логируем каждый ключ-значение
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'editdogAction.php', true);

            xhr.onload = function () {
                console.log('Status: ' + xhr.status);  // Выводим код статуса ответа
                console.log('Response: ' + xhr.responseText);  // Выводим ответ от сервера

                if (xhr.status === 200) {
                    try {
                        let response = JSON.parse(xhr.responseText); // пытаемся распарсить JSON

                        if (response.success) {
                            alert('Данные успешно обновлены!');
                            location.reload(); // Перезагружаем страницу
                        } else {
                            console.error('Ошибка на сервере: ', response.message);
                            alert('Произошла ошибка: ' + response.message);
                        }
                    } catch (e) {
                        console.error('Ошибка парсинга JSON: ', e);
                        alert('Ошибка при обработке ответа от сервера');
                    }
                } else {
                    console.error('Ошибка запроса: ', xhr.status);
                    alert('Ошибка запроса: ' + xhr.status);
                }
            };

            xhr.onerror = function () {
                console.error('Ошибка при выполнении запроса');
                alert('Ошибка при выполнении запроса');
            };

            // Отправляем данные
            console.log('Отправляем данные:', Object.fromEntries(formData.entries()));
            xhr.send(formData);
        });
    });

    // Обработчики для кнопок "Изменить" и "Удалить"
    document.querySelectorAll('.form__item input[type="submit"]').forEach(button => {
        button.addEventListener('click', function (event) {
            let action = this.getAttribute('data-action'); // Получаем значение из data-action
            let form = this.closest('form'); // Ищем ближайшую форму
            let actionField = form.querySelector('#action');

            if (!action) {
                console.error('Ошибка: кнопка без data-action');
                return;
            }

            if (actionField) {
                actionField.value = action; // Устанавливаем значение в скрытое поле
                console.log(`Action set to: ${action}`);
            } else {
                console.error('Ошибка: скрытое поле #action не найдено в форме');
            }
        });
    });
});
