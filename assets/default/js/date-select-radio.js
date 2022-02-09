$(document).ready(function () {
    $('.date_select_radio').change(function () {
        function setDateZero(date) {
            return date < 10 ? '0' + date : date;
        }

        var param = $(this).val();
        var date = new Date();
        var y = date.getFullYear();
        var m = date.getMonth();
        var thisMonthFirstDay = new Date(y, m, 1);
        var previousMonthFirstDay = new Date(y, m - 1, 1);
        var previousMonthLastDay = new Date(y, m, 0);
        var today = new Date();

        var fromDate = null;
        var toDate = null;

        var yesterday = (function () {
            this.setDate(this.getDate() - 1);
            return this;
        }).call(new Date);

        switch (parseInt(param)) {
            case 1:
                fromDate = setDateZero(today.getMonth() + 1) +
                    '/' + setDateZero(today.getDate()) +
                    '/' + today.getFullYear();

                toDate = fromDate;
            break;

            case 2:
                fromDate = setDateZero(yesterday.getMonth() + 1) +
                    '/' + setDateZero(yesterday.getDate()) +
                    '/' + yesterday.getFullYear();

                toDate = fromDate;
            break;

            case 3:
                fromDate = setDateZero(thisMonthFirstDay.getMonth() + 1) +
                    '/' + setDateZero(thisMonthFirstDay.getDate()) +
                    '/' + thisMonthFirstDay.getFullYear();

                toDate = setDateZero(today.getMonth() + 1) +
                    '/' + setDateZero(today.getDate()) +
                    '/' + today.getFullYear();
            break;

            case 4:
                fromDate = setDateZero(previousMonthFirstDay.getMonth() + 1) +
                    '/' + setDateZero(previousMonthFirstDay.getDate()) +
                    '/' + previousMonthFirstDay.getFullYear();

                toDate = setDateZero(today.getMonth() + 1) +
                    '/' + setDateZero(today.getDate()) +
                    '/' + today.getFullYear();
            break;

            case 5:
                fromDate = setDateZero(previousMonthFirstDay.getMonth() + 1) +
                    '/' + setDateZero(previousMonthFirstDay.getDate()) +
                    '/' + previousMonthFirstDay.getFullYear();

                toDate = setDateZero(previousMonthLastDay.getMonth() + 1) +
                    '/' + setDateZero(previousMonthLastDay.getDate()) +
                    '/' + previousMonthLastDay.getFullYear();
            break;
        }

        $("#from_date").val(fromDate);
        $("#to_date").val(toDate);
    });
});

