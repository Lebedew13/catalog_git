document.addEventListener("DOMContentLoaded", function(){
// Проверка, поддерживает ли браузер Web Speech API
if ('webkitSpeechRecognition' in window) {
    var recognition = new webkitSpeechRecognition();
    recognition.continuous = true;  // Поддержка непрерывного ввода
    recognition.interimResults = true;  // Частичные результаты

    // Обработчик при получении результатов
    recognition.onresult = function(event) {
        var transcript = '';
        for (var i = event.resultIndex; i < event.results.length; i++) {
            transcript += event.results[i][0].transcript;
        }
        // Отображаем результат в поле ввода
        document.getElementById('voiceInput').value = transcript;
    };

    // Начать запись
    document.getElementById('startButton').onclick = function() {
        recognition.start();
    };

    // Обработчик ошибок
    recognition.onerror = function(event) {
        console.error("Ошибка распознавания: ", event.error);
    };

    // Обработчик, когда распознавание завершено
    recognition.onend = function() {
        console.log("Распознавание завершено.");
    };
} else {
    alert('Web Speech API не поддерживается в вашем браузере!');
}
})



document.addEventListener("DOMContentLoaded", function () {
    const classFilter = document.getElementById("classFilter");
    const genderFilter = document.getElementById("genderFilter");
    const dogItems = document.querySelectorAll(".list__dog-item");

    function filterDogs() {
        const selectedClass = classFilter.value;
        const selectedGender = genderFilter.value;

        dogItems.forEach(item => {
            const dogClass = item.getAttribute("data-class");
            const dogGender = item.getAttribute("data-gender");

            const classMatch = selectedClass === "" || dogClass === selectedClass;
            const genderMatch = selectedGender === "" || dogGender === selectedGender;

            if (classMatch && genderMatch) {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });
    }

    classFilter.addEventListener("change", filterDogs);
    genderFilter.addEventListener("change", filterDogs);
});
