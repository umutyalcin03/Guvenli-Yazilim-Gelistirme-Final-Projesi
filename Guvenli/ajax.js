document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.querySelector('.search-form');
    const searchInput = searchForm.querySelector('input[name="search"]');
    const newsContainer = document.querySelector('.container');

    searchForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const searchTerm = searchInput.value;

        const xhr = new XMLHttpRequest();
        xhr.open('GET', `search_news.php?search=${encodeURIComponent(searchTerm)}`, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                newsContainer.innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    });
});