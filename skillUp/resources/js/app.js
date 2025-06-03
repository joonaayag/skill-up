import './bootstrap';
import Alpine from 'alpinejs'
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);


window.Alpine = Alpine

Alpine.start()

window.addEventListener('load', () => {
    gsap.from("#welcome-section", {
        scrollTrigger: {
            trigger: "#welcome-section",
            start: "top 80%",
            toggleActions: "play none none none"
        },
        opacity: 0,
        y: 80,
        duration: 1.2,
        ease: "power3.out"
    });
});
