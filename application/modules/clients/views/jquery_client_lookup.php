<script type="text/javascript">
    $(function () {
        $('#client_name').keypress(function () {
            var self = $(this);

            $.post("/clients/ajax/name_query", {
                query: self.val()
            }, function (data) {
                var json_response = eval('(' + data + ')');
                self.data('typeahead').source = json_response;
            });
        });
    });
</script>
