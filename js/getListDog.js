document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.form__listDog').forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            let showid = document.getElementById('showid').value;
            let ringid = document.getElementById('ringid').value;

            // Отправляем запрос на сервер для получения списка собак
            if (confirm('Сформировать список собак?')) {

                // Отправляем запрос на сервер
                fetch('getDogList.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        showid: showid,
                        ringid: ringid
                    }),  // Передаем данные как JSON
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Список сформирован');
                        
                        // Скрываем кнопку
                        const submitButton = form.querySelector('input[type="submit"]');
                        if (submitButton) {
                            submitButton.style.display = 'none';
                        }

                        // Перезагружаем страницу (если нужно)
                         location.reload();
                    } else {
                        alert('Ошибка при формировании списка: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Ошибка: ' + error);
                    console.log(error);
                });
            }
        });
    });
});
