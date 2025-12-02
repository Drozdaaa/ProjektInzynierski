function applyFilter(status) {
    const url = new URL(window.location.href);
    url.searchParams.set('status', status);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}
