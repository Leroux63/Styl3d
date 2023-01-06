window.onload = ()=>{
     //gestion des boutons "supprimer"
     let links = document.querySelectorAll("[data-delete]")
     console.log(links)
     //on boucle sur links
     for (link of links){
          //on écoute le clic
          link.addEventListener("click", function (e){
               //on empeche la navigation
               e.preventDefault()
               //on demande confirmation
               if (confirm("Voulez-vous supprimer cette image?")){
                    //on envoie une requete Ajax vers le href du lien avec la méthode DELETE
                    //d'abord une promesse avec le fetch
                    fetch(this.getAttribute("href"),{
                         method: "DELETE",
                         headers: {
                              "X-Request-With": "XMLHttpRequest",
                              "Content-Type": "application/json"
                         },
                         //on récupère le token dans le data
                         body: JSON.stringify({"_token": this.dataset.token})
                    }).then(
                        //on récupère la réponse en JSON
                        response=>response.json()
                    ).then(data =>{
                         if (data.success)
                              this.parentElement.remove()
                         else
                              alert(data.error)
                    }).catch(e => alert(e))
               }
          })
     }
}
