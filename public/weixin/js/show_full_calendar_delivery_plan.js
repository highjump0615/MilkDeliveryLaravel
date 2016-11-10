 $(function () {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                },
                firstDay: 0,
                editable: true,
                events: [
                    {
                        title: '2',
                        start: '2016-09-28',
                        //className:'ypsrl'

                    },
                    {
                        //title: '2',
                        start: '2016-09-28',
                        rendering: 'background',
                        color: '#00a040'
                    },
                    {
                        title: '5',
                        start: '2016-09-29',
                    },
                    {
                        //title: '5',
                        start: '2016-09-29',
                        rendering: 'background',
                        color: '#00a040'
                    },
                    {
                        title: '3',
                        start: '2016-09-30',
                    },
                    {
                        //title: '3',
                        start: '2016-09-30',
                        rendering: 'background',
                        color: '#00a040'
                    }
                ],
                dayClick: function (date, jsEvent, view) {
                    $("#message-content").html(date.format());
                    $("#dialog-message").dialog({
                        modal: true,
                        buttons: {
                            Ok: function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            });

        });