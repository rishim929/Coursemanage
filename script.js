document.getElementById("search").addEventListener("keyup", function () {
    if (this.value.trim() === "") {
        // If search is empty, reload the page or show all
        location.reload();
    } else {
        fetch("ajax_search.php?q=" + encodeURIComponent(this.value))
            .then(res => res.text())
            .then(data => {
                document.getElementById("courseTableBody").innerHTML = data;
            });
    }
});

document.getElementById("darkModeToggle").addEventListener("click", function() {
    document.body.classList.toggle("dark-mode");
});
