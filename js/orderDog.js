document.addEventListener("DOMContentLoaded", function () {
    let sortable = new Sortable(document.getElementById("sortable-list"), {
        animation: 150,
        onEnd: function () {
            saveOrder();
        }
    });
    let sortableSub = new Sortable(document.getElementById("sub-breeds"), {
        animation: 150,
        onEnd: function () {
            saveOrder();
        }
    });

    function saveOrder() {
        let items = document.querySelectorAll(".breed-item");
        let subitems = document.querySelectorAll(".sub-breed");
        let order = [];
        //const urlParams = new URLSearchParams(window.location.search);
        const showid = document.getElementById('showid').value;
        const ringid = document.getElementById('ringid').value;

        items.forEach((item, index) => {
            let breed = item.textContent.trim(); // Убираем лишние пробелы и переносы строк
            breed = breed.replace(/\n/g, '').replace(/\s{2,}/g, ' '); // Убираем символы новой строки и лишние пробелы
            
            // Очищаем от типов породы
            breed = breed.replace(/(Классик|Стандарт|XL|Покет|Экзот|Микро)/g, '').trim(); // Убираем типы породы (можно добавить другие типы по мере необходимости)

            order.push({
                breed: breed, // Теперь передаем очищенную породу
                type_breed: type_breed,
                position: index + 1
            });
        });

        subitems.forEach((item, index) => {
            let typeBreed = item.textContent.trim();
            typeBreed = typeBreed.replace(/\n/g, '').replace(/\s{2,}/g, ' '); // Очищаем подтипы
            order.push({
                type_breed: typeBreed, // Теперь передаем очищенный подтип
                sub_position: index + 1.1 // Пример использования дробной позиции
            });
        });

        const dataToSend = {
            order: order,
            showid: showid,
            ringid: ringid
        };

        console.log("Сохраняем порядок:", dataToSend);

        fetch("save_order.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(dataToSend)
        })
        .then(response => response.json())
        .then(data => {
            console.log("Ответ сервера:", data);
            if (data.status === "success") {
                location.reload();
            } else {
                alert("Ошибка сохранения: " + data.message);
            }
        })
        .catch(error => console.error("Ошибка сохранения:", error));
    }
});
