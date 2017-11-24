<script>
    $(function () {
        var addresses = [
            @foreach($addresses as $address)
            "{{ str_replace( '/\\r?\\n|\\r/'    , '', $address ) }}",
            @endforeach
        ];

        $("#search").autocomplete({
            source: addresses
        });
    });
</script>