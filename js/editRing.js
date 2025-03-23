document.addEventListener("DOMContentLoaded", function(){
    const $btnAddRing = document.getElementById('addRing');
    const $formAddRing = document.getElementById('formAddRing');

    $btnAddRing.addEventListener('click', function(){
        $formAddRing.style.display = 'flex';
    });

    // Закрытие модального окна
    document.querySelectorAll('.close_modal').forEach(closeButton => {
        closeButton.addEventListener('click', function () {
            $formAddRing.style.display = 'none';
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
    const form = document.querySelector('#formAddRing form');
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const nameInput = form.querySelector('input[type="text"]');
        const showIdInput = form.querySelector('input[type="hidden"]');

        if (!nameInput || !showIdInput) {
            console.error("Ошибка: Не найдены необходимые поля в форме!");
            return; // Останавливаем выполнение кода
        }

        const ringName = nameInput.value;
        const showid = showIdInput.value;

        // Создаем данные для отправки
        const formData = {
            name: ringName,
            showid: showid
        };

        console.log("Отправка запроса на сервер:", formData); // Лог перед отправкой

        fetch('editRing.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(response => {
            console.log("Ответ получен:", response);
            return response.json();
        })
        .then(data => {
            console.log("JSON ответ:", data);
            if (data.success) {
                alert("Ринг добавлен!");
                location.reload();
            } else {
                alert("Ошибка: " + data.message);
            }
        })
        .catch(error => {
            console.error("Ошибка при отправке запроса:", error);
        });
    });

    // Удаление ринга
    document.querySelectorAll('.delete-ring').forEach(button => {
        button.addEventListener('click', function () {
            const ringId = this.dataset.id;
            console.log("Удаление ринга, ID:", ringId); // Лог
    
            if (!confirm("Вы уверены, что хотите удалить этот ринг?")) return;
    
            fetch('editRing.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ringid: ringId })
            })
            .then(response => {
                console.log("Ответ получен:", response);
                return response.json();
            })
            .then(data => {
                console.log("JSON ответ:", data);
                if (data.success) {
                    this.closest('.ring-item').remove();
                    alert("Ринг удален успешно!");
                } else {
                    alert("Ошибка: " + data.message);
                }
            })
            .catch(error => {
                console.error("Ошибка запроса:", error);
            });
        });
    });
});
