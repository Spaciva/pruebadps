</div>

<script>
/* Auto-wrap tables for horizontal scroll on mobile */
document.querySelectorAll('table').forEach(function(t){
    if (!t.parentElement.classList.contains('table-wrap')) {
        var w = document.createElement('div');
        w.className = 'table-wrap';
        t.parentNode.insertBefore(w, t);
        w.appendChild(t);
    }
});
</script>

</body>
</html>