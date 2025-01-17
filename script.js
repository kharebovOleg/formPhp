document.querySelectorAll('.menubutton').forEach(function(e) {
    e.addEventListener('click', function() {
      document.querySelectorAll('.menubutton')
      .forEach(e => {
        e.style.backgroundColor = "rgb(218, 216, 216)";
        e.style.fontWeight = "normal";
    });  

      this.style.backgroundColor = "white";
      this.style.fontWeight = "bold";
    })
  });