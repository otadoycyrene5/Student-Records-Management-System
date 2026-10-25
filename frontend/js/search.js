function initializeSearch() {
    if (!searchInput) {
        return;
    }
    searchInput.value = parameters.get('q') || '';
    document.getElementById('clear-search').hidden = searchInput.value === '';
}

function getSearchTerm() {
    if (!searchInput) {
        return '';
    }
    return searchInput.value.trim();
}
