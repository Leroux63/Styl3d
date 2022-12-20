window.onload = () => {
    //on va chercher les sections du document
    let sections = document.querySelectorAll("section");
    // console.log(sections);

    // on va chercher la div "scroll-icons"
    let icons = document.querySelector(".scroll-icons");

    //on initialise l'Intersection observer
    let observer = new IntersectionObserver(observables => {
        // console.log(observables);
        for (let observable of observables) {
            if (observable.intersectionRatio > 0.5) {
                //plus de la moitié de la section est visible à l'écran
                //on récupère topus les cercles pour les désactiver
                let circles = document.querySelectorAll(".scroll-icons .circle");
                //on boucle sur les cercles pour les désactiver
                for (let circle of circles) {
                    circle.classList.remove("active");
                }
                //on va chercher le cercle correspondant à la section active
                let circle = document.querySelector(`
                [data-id=${observable.target.id}]`);
                console.log(circle);
                //on active le cercle
                circle.classList.add("active");
            }
        }
    }, {
        threshold: [0.5]
    });

    //on boucle sur les sections
    sections.forEach((section, index) => {
        //on crée une icone
        let icon = document.createElement("div");

        //on lui donne la classe "circle"
        icon.classList.add("circle");

        //on ajoute la classe "active" à la première icone
        if (index === 0) {
            icon.classList.add("active");
        }
        //on ajoute le data-id à l'icone
        icon.dataset.id = section.id;
        //on ajoute les icones dans le document
        icons.appendChild(icon);
        //On ajoute la section à l'intersection Observer
        observer.observe(section);
    });

}