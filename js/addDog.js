document.addEventListener('DOMContentLoaded', function () {
    // Открытие модального окна
        let button = document.getElementById('modal__add');
            button.addEventListener('click', function () {
            document.getElementById('modal-add').style.display = 'flex';
        });
    

    // Закрытие модального окна
    document.querySelectorAll('.close').forEach(closeButton => {
        closeButton.addEventListener('click', function () {
                document.getElementById('modal-add').style.display = 'none';
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

});

document.addEventListener("DOMContentLoaded", function() {
 // Обработчик изменения позиции
    const addForm = document.getElementById("add-form");

    // Обработчик поиска
    addForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const showid = document.getElementById("showid").value;
        const ringid = document.getElementById("ringid").value;
        const breed = document.getElementById("breed").value;
        const type_breed = document.getElementById("type_breed").value;
        const class_breed = document.getElementById("class_breed").value;
        const name_dog = document.getElementById("name_dog").value;
        const gender = document.getElementById("gender").value;
        const color = document.getElementById("color").value;
        const owner = document.getElementById("owner").value;
        const breeder = document.getElementById("breeder").value;

        const dataToSend = {
            showid: showid,
            ringid: ringid,
            breed: breed,
            type_breed: type_breed,
            class_breed: class_breed,
            name_dog: name_dog,
            gender: gender,
            color, color,
            owner: owner,
            breeder: breeder
        };

        console.log("Добавляем собаку:", dataToSend);

        fetch("addDog.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(dataToSend)
        })
        .then(response => response.json())
        .then(data => {
            console.log("Ответ сервера:", data);
            if (data.status === "success") {
                alert('Собака успешно добавлена!');
                location.reload();
            } else {
                alert("Ошибка сохранения: " + data.message);
            }
        })
        .catch(error => console.error("Ошибка сохранения:", error));
        });
    });
