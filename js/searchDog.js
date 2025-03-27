

document.addEventListener('DOMContentLoaded', function () {
    // Открытие модального окна
        let button = document.getElementById('modal__search');
            button.addEventListener('click', function () {
            document.getElementById('modal-search').style.display = 'flex';
        });
    

    // Закрытие модального окна
    document.querySelectorAll('.close').forEach(closeButton => {
        closeButton.addEventListener('click', function () {
                document.getElementById('modal-search').style.display = 'none';
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
document.addEventListener("DOMContentLoaded", function () {
    const searchForm = document.getElementById("search-form");
    const searchResults = document.getElementById("search-results");

    // Обработчик поиска
    searchForm.addEventListener("submit", function (event) {
        event.preventDefault();
        const nameDog = document.getElementById("search-input").value.trim();
        const showid = document.getElementById("showid").value;
        const ringid = document.getElementById("ringid").value;

        if (nameDog === "") return;

        fetch("searchDog.php?name_dog=" + encodeURIComponent(nameDog) + "&showid=" + showid + "&ringid=" + ringid)
            .then(response => response.text())
            .then(data => {
                searchResults.innerHTML = data; // Обновляем результаты
            })
            .catch(error => console.error("Ошибка поиска:", error));
    });

    // Обработчик изменения позиции
    searchResults.addEventListener("submit", function (event) {
        if (event.target.classList.contains("position-form")) {
            event.preventDefault();
            const formData = new FormData(event.target);

            fetch("update_position.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data); // Выводим сообщение об успехе
                document.getElementById("modal-search").style.display = "none";
                location.reload();
            })
            .catch(error => console.error("Ошибка обновления позиции:", error));
        }
    });

    // Закрытие модального окна
    document.querySelector(".close").addEventListener("click", function () {
        document.getElementById("modal-search").style.display = "none";
    });

    document.getElementById("modal__search").addEventListener("click", function () {
        document.getElementById("modal-search").style.display = "flex";
    });
});