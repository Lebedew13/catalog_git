function idbc_show(){ //берем значение из названия выставки
    let a = document.getElementById('show_idbc');
    const now = new Date(a.value);
    return now;
}

function bday(){ //берем значение из даты рожения
    let b = document.getElementById('birthday');                 
    let date = new Date(b.value);
    let nowd = idbc_show();

    let yearNow = nowd.getFullYear();
    let monthNow = nowd.getMonth();
    let dateNow = nowd.getDate();



    let yearDob = date.getFullYear();
    let monthDob = date.getMonth();
    let dateDob = date.getDate();
  
    /*Расчет возраста**/

    age = {};  
    ageString = '';
    yearString = '';
    monthString = '';
    dayString = '';


    let yearAge = yearNow - yearDob ;

    if (monthNow >= monthDob){
    monthAge =  monthNow - monthDob ;
    }else {
    yearAge--;
    monthAge = 12 + monthNow - monthDob;
    }

    if (dateNow >= dateDob){
    dateAge = dateNow - dateDob;
    }else {
    monthAge--;
    dateAge = 31 + dateNow - dateDob;

    if (monthAge < 0) {
    monthAge = 11;
    yearAge--;
    }
    }

    age = {
    years: yearAge,
    months: monthAge,
    days: dateAge
    };

    if ( age.years > 1 ) yearString = 'years';
    else yearString = 'year';
    if ( age.months > 1 ) monthString = 'months';
    else monthString = ' month';
    if ( age.days > 1 ) dayString = 'days';
    else dayString = ' day';




    a = document.getElementById('class_breed');
    b = document.getElementById('class__breed-15');


if(document.getElementById('breed_s').value === 'Другое' || document.getElementById('breed_s').value === 'Подтверждение породы'){

if(age.years == 0 && age.months >= 3  &&  age.months < 6){
a.value = '';
a.value += 'Бэби';

}else if(age.years == 0 && age.months >= 6 && age.months < 9){
a.value = ''; 
a.value += 'Щенок';

}else if(age.years == 0 && age.months >= 9 && age.months < 12){
a.value = ''; 
a.value += 'Юниор';
}
else if(age.years >= 1 && age.months <= 3 && age.years < 2){
a.value = '';
a.value += 'Взрослые'

}else if(age.years >= 2){
a.value = ''; 
a.value += 'Зрелые (2+)';

}
}else{
if(age.years == 0 && age.months >= 3  &&  age.months < 6){
a.style.cssText ='display: block';

document.getElementById('c_mini').style.cssText = 'display: none'
document.getElementById('c_maxi').style.cssText = 'display: none'
document.getElementById('c_exo').style.cssText = 'display: none'
a.value = '';
a.value += 'Бэби';

}else if(age.years == 0 && age.months >= 6 && age.months < 9){
a.style.cssText ='display: block';

document.getElementById('c_mini').style.cssText = 'display: none'
document.getElementById('c_maxi').style.cssText = 'display: none'
document.getElementById('c_exo').style.cssText = 'display: none'
a.value = ''; 
if(document.getElementById('type_breed').value === 'Покет' || document.getElementById('type_breed').value === 'Микро'){
a.value = ''; 
a.value += 'Щенок мини';
} else if(document.getElementById('type_breed').value === 'Экзот'){
a.value = ''; 
a.value += 'Щенок экзот';
}else{
a.value = ''; 
a.value += 'Щенок макси';
}

}else if(age.years == 0 && age.months >= 9 && age.months <= 12){
a.style.cssText ='display: block';

document.getElementById('c_mini').style.cssText = 'display: none'
document.getElementById('c_maxi').style.cssText = 'display: none'
document.getElementById('c_exo').style.cssText = 'display: none'
a.value = ''; 

if(document.getElementById('type_breed').value === 'Покет' || document.getElementById('type_breed').value === 'Микро'){
a.value = ''; 
a.value += 'Юниор мини';
} else if(document.getElementById('type_breed').value === 'Экзот'){
a.value = ''; 
a.value += 'Юниор экзот';
}else{
a.value = ''; 
a.value += 'Юниор макси';
}

}else if( age.months < 3 && age.years == 1  && age.years < 2){
a.value='';
a.style.cssText ='display: none';



if(document.getElementById('type_breed').value === 'Покет' || document.getElementById('type_breed').value === 'Микро'){
a.value = ''; 
document.getElementById('c_mini').style.cssText = 'display: block';
document.getElementById('c_exo').style.cssText = 'display: none';
document.getElementById('c_maxi').style.cssText = 'display: none';
a.value += document.getElementById('c_mini').value;
} else if(document.getElementById('type_breed').value == 'Экзот'){
a.value = ''; 
document.getElementById('c_mini').style.cssText = 'display: none';
document.getElementById('c_exo').style.cssText = 'display: block';
document.getElementById('c_maxi').style.cssText = 'display: none';
a.value += document.getElementById('c_exo').value;
}else{
a.value = ''; 
document.getElementById('c_mini').style.cssText = 'display: none';
document.getElementById('c_exo').style.cssText = 'display: none';
document.getElementById('c_maxi').style.cssText = 'display: block';

a.value += document.getElementById('c_maxi').value;

}
}
else if( age.months >= 3 && age.years === 1 && age.years <= 2){
    a.style.cssText ='display: block';
    document.getElementById('c_mini').style.cssText = 'display: none';
    document.getElementById('c_maxi').style.cssText = 'display: none';
    document.getElementById('c_exo').style.cssText = 'display: none';
    a.value = '';
    a.value = 'Взрослые';

}else if(age.years > 2){
a.style.cssText ='display: block';
document.getElementById('c_mini').style.cssText = 'display: none';
document.getElementById('c_maxi').style.cssText = 'display: none';
document.getElementById('c_exo').style.cssText = 'display: none';
a.value = ''; 
a.value += 'Зрелые (2+)';


}



}



if(age.years < 2000){

let al = document.getElementById('age__p-text');
al.style.cssText = 'display: block';

// let year = age.years;
// let month = age.months;
// let days = age.days;



result = '';
resultm = '';


if(age.months >= 11 && age.months <= 12){
    resultm = 'месяцев'
}else{
    if(age.months === 1){
        resultm = 'месяц'
    }else if(age.months >= 2 && age.months <= 4){
        resultm = 'месяца';
    }else{
        resultm = 'месяцев'
    }
}




if( age.years >= 10 &&  age.years <= 20){
    result = 'лет';
}else{
    count = age.years % 10;
    if(count === 1){
        result = 'год';
    }else if(count >= 2 && count <= 4){
        result = 'года';
    }else{
        result = 'лет';
    }
}

}

let p = document.getElementById('age_p');

if (age.years === 0){
    p.innerText = "Возраст собаки на момент выставки"+ " " + age.months + " " + resultm;
}else if(age.months === 0){
    p.innerText = "Возраст собаки на момент выставки" + " " + age.years + " " + result;
}else{
    p.innerText = "Возраст собаки на момент выставки" + " " + age.years + " " + result + " " + " " + age.months + " " + resultm;
}



  
}

document.addEventListener("DOMContentLoaded", function (){
    function capitalize(input) {
        input.value = input.value.replace(/( |^)[а-яёa-z]/g, function(u){ return u.toUpperCase(); }  );
      }
      window.document.getElementById('owner').addEventListener('input', function(){
        capitalize(this);
      });
      document.getElementById('breeder').addEventListener('input', function(){
        capitalize(this);
      });
      document.getElementById('city').addEventListener('input', function(){
        capitalize(this);
      });

       document.querySelector('.main__form').addEventListener('submit', function (e) {
        // Показать спиннер
        document.getElementById('loading-spinner').style.display = 'flex';
    });

    function allowOnlyRussian(input) {
        input.value = input.value.replace(/[^а-яА-ЯёЁ\s]/g, ''); // Разрешены только русские буквы и пробелы
    }

    // Привязка обработчика к полям
    const ownerInput = document.getElementById('owner');
    const breederInput = document.getElementById('breeder');

    ownerInput.addEventListener('input', function () {
        allowOnlyRussian(this);
    });

    breederInput.addEventListener('input', function () {
    allowOnlyRussian(this);
  });
});

function click(){
    $info1 = document.getElementById('info1');
    $info2 = document.getElementById('info2');
    $info3 = document.getElementById('info3');
    if($info1.checked && $info2.checked && $info3.checked){
        document.getElementById('sub').disabled = false;
    }else{
        document.getElementById('sub').disabled = true;
    }
 }