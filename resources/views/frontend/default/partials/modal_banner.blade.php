@php
    $_mb = ModalBanner::getFirst();
@endphp
@if($_mb)
    <script>
        var m = UIkit.modal('{!! $_mb !!}');
        var nd = new Date();
        var ed = new Date(nd.getFullYear(), nd.getMonth(), nd.getDate(), 12, nd.getMinutes() + 2, 0);
        m.show();
        $('body').delegate(`#modal-banner`, 'hidden', function (event) {
            if (event.target === event.currentTarget) m.$destroy(true);
            document.cookie = "modalBanner=1; expires=" + ed.toUTCString() + ";";
            console.log(ed, "modalBanner=1; expires=" + ed.toUTCString() + ";");
        });
    </script>
@endif
