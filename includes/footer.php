<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    // Animasi saat scroll
    const items = document.querySelectorAll(".animate-on-scroll");

    const observer = new IntersectionObserver((entries)=>{
        entries.forEach(entry=>{
            if(entry.isIntersecting){
                entry.target.classList.add("animate__animated","animate__fadeInUp");
                observer.unobserve(entry.target);
            }
        });
    });

    items.forEach(item=>observer.observe(item));

});
</script>

</body>
</html>