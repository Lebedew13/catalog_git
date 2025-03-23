document.addEventListener("DOMContentLoaded", function(){
    $btnAddRing =  document.getElementById('addRing');
    $formAddRing = document.getElementById('formAddRing');
    $btnAddRing.addEventListener('click', function(){
        $formAddRing.style.display = 'flex';
    });

    // Закрытие модального окна
    document.querySelectorAll('.close_modal').forEach(closeButton => {
        closeButton.addEventListener('click', function () {
            $formAddRing = document.getElementById('formAddRing');
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

        // Получаем значения из формы
        const ringName = form.querySelector('input[type="text"]').value;
        const showid = form.querySelector('input[type="hidden"]').value;

        // Создаем данные для отправки
        const formData = {
            name: ringName,
            showid: showid
        };

        // Отправляем запрос на сервер
        fetch('editRing.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Закрываем форму
                $formAddRing.style.display = 'none';
                form.querySelector('input[type="text"]').value = '';
                // Обновляем список рингов на странице
                const ringList = document.querySelector('.ring-item');
                const newRingItem = document.createElement('a');
                newRingItem.href = `selectRing.php?showid=${data.showid}&ringid=${data.ringid}`;
                newRingItem.textContent = data.ringName;
                ringList.appendChild(newRingItem);
                
                // Можно добавить сообщение об успешном добавлении ринга
                alert('Ринг добавлен успешно!');
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка при отправке запроса:', error);
            alert('Произошла ошибка. Попробуйте снова.');
        });
    });

    document.querySelectorAll('.delete-ring').forEach(button => {
        button.addEventListener('click', function () {
            const ringId = this.dataset.id;

            if (!confirm("Вы уверены, что хотите удалить этот ринг?")) return;

            fetch('editRing.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ringid: ringId })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Ответ сервера:", data); // ← Добавлено
                if (data.success) {
                    this.closest('.ring-item').remove();
                    alert("Ринг удален успешно!");
                } else {
                    alert("Ошибка: " + data.message);
                }
            })
            .catch(error => {
                console.error("Ошибка запроса:", error);
                alert("Произошла ошибка. Попробуйте снова.");
            });
        });
    });

});