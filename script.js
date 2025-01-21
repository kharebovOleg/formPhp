// Зполнение вариантов выбора даты
const days = [
  1,2,3,4,5,6,7,8,9,10,
  11,12,13,14,15,16,17,
  18,19,20,21,22,23,24,
  25,26,27,28,29,30,31
];

const months = [
  'января', 'февраля','марта','апреля',
  'мая','июня','июля','августа',
  'сентября','октября', 'ноября', 'декабря'
];

const years = fillYears();

const daysId = 'days';
const monthsId = 'months';
const yearsId = 'years';

function fillDataList(arr, id) {
  let select = document.getElementById(String (id));
  arr.forEach(function(e) {
    const option = new Option(e, String(e));
    select.add(option);
  });
}

function fillYears() {
  const years = [];
  const currentYear = new Date().getFullYear() - 10;
  for(let i = currentYear; i >= 1900; i--) {
    years.push(i);
  }
  return years;
}

fillDataList(days, daysId);
fillDataList(months, monthsId);
fillDataList(years, yearsId);


// Эффект переключения кнопок панели меню
makeButtonsInteractive();

function makeButtonsInteractive() {
  document.querySelectorAll('.menubutton').forEach(function(e) {
    e.addEventListener('click', function() {
      document.querySelectorAll('.menubutton')
      .forEach(e => {
        e.style.backgroundColor = "rgb(243, 243, 243)";
        e.style.fontWeight = "normal";
    });  
  
      this.style.backgroundColor = "white";
      this.style.fontWeight = "bold";
    })
  });
}

// Валидация полей

// доделать функцию отключения кнопки "отправить" при нарушении валидации

const form = document.querySelector('.form');
const inputList = Array.from(form.querySelectorAll('.form__type-input'));
const buttonElement = form.querySelector('.button');
const formErrorElement = form.querySelector('.form__empty-error');

startValidation(); // вкл/выкл валидации

function startValidation() {
  //toggleButton(); // некорректно работет всегда блокирует кнопку, а должна только при валидации
  form.addEventListener('submit', (event) => {
    event.preventDefault();
    if (hasInvalidInput()) {
      formError();
      inputList.forEach((inputElement) => {
        checkInputValidity(inputElement);
        toggleInputError(inputElement);
      })
    }
  });
  inputList.forEach((inputElement) => {
    inputElement.addEventListener('input', () => {
      checkInputValidity(inputElement);
      //toggleButton();
    })
    inputElement.addEventListener('blur', () => {
      toggleInputError(inputElement);
    })
    inputElement.addEventListener('focus', () => {
      toggleErrorSpan(inputElement);
    })
  })
}

function checkInputValidity(inputElement) {
  if (inputElement.validity.patternMismatch) {
    inputElement.setCustomValidity(inputElement.dataset.errorMessage);
  } else {
    inputElement.setCustomValidity(checkLengthMismatch(inputElement));
  }
}

function checkLengthMismatch(inputElement) {
  if (inputElement.type !== 'text') {
    return '';
  }
  const valueLength = inputElement.value.trim().length
  if (valueLength < inputElement.minLength) {
    return `Минимальное количество символов: ${inputElement.minLength}`;
  }
  return '';
}

function hasInvalidInput() { //работает правильно, если в элементе inputlist нарушена валидация вернет false 
  return inputList.some(inputElement => !inputElement.validity.valid); // тут может надо поставить "!", а может нет
}

function toggleInputError(inputElement) {
  if (!inputElement.validity.valid) {
    toggleErrorSpan(inputElement, inputElement.validationMessage);
  } else {
    toggleErrorSpan(inputElement);
  }
}

function toggleErrorSpan(inputElement, errorMessage){
  const errorElement = document.querySelector(`.${inputElement.id}-error`);
  if (errorMessage) {
    inputElement.classList.add('form__type-input-error');
    errorElement.textContent = errorMessage;
    errorElement.classList.add('form__error-active');
  } else {
    inputElement.classList.remove('form__type-input-error');
    errorElement.textContent = '';
    errorElement.classList.remove('form__error-active');
  }
}

// делает кнопку отправки формы неактивной

function toggleButton() {
  if (hasInvalidInput()) {
    buttonElement.classList.add('button-inactive');
    buttonElement.setAttribute('disabled', 'true');
  } else {
    buttonElement.classList.remove('button-inactive');
    buttonElement.setAttribute('disabled', 'false');
    formErrorElement.textContent = '';
  }
}

function formError() {
  const errorMessage = 'Форма заполнена с ошибками';
  formErrorElement.textContent = errorMessage;
}

// Отправка формы

  document.querySelector('form').onsubmit = async e => {
    e.preventDefault();
    await fetch('form.php', { method: 'POST', body: new FormData(e.target) })
      .then(response => response.text())
      .then(responseText => console.log(responseText))
      .catch(error => console.error('Ошибка:', error));
  };

