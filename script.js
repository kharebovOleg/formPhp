
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

makeButtonsInteactive();
fillDataList(days, daysId);
fillDataList(months, monthsId);
fillDataList(years, yearsId);


function makeButtonsInteactive() {
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




document.querySelector('form').onsubmit = async e => {
  e.preventDefault();
  let response = await fetch('form.php', { method: 'POST', body: new FormData(e.target) });
  let result = await response.text();
  console.log(result);
};





