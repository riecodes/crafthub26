// Search form submission using AJAX
$("#searchForm").submit(function(event){
    event.preventDefault(); // Prevent form submission

    var searchQuery = $("#searchbar").val().trim();

    // Redirect to searchpage.php with search query as GET parameter
    window.location.href = "searchpage.php?searchbar=" + encodeURIComponent(searchQuery);
});