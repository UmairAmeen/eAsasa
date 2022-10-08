                  function makeVisible(e) {
                    var jj = $('#'+e.id).children("div");
                    var styling = document.getElementById(jj[0].id);

                    var hideAll = document.getElementsByClassName('selectalltohide');
                    var count = hideAll.length;

                    for (let index = 0; index < count; index++) {
                      if(hideAll[index].id != jj[0].id){
                        hideAll[index].style.display = "none";}
                    }

                    if(styling.style.display == "block"){
                      styling.style.display = "none";
                    }
                    else{
                      styling.style.display = "block";
                      styling.style.verticalAlign =  "middle";
                      styling.style.marginLeft = "16px";
                    }
                  }                  
