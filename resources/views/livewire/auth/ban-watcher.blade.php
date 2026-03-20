<div
    x-on:force-logout.window="
        fetch('/logout', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
        }).finally(() => { window.location.href = '/login?banned=1'; })
    "
></div>
