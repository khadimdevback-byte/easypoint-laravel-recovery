<x-app-layout>
    <div class=" px-4 md:w-9/12  2xl:w-8/12 mx-auto pb-10">
        <div class="w-full lg:px-6 pt-5 mx-auto">
            <div class="lg:p-10 p-4 shadow-md rounded-md border">
                <h2 class=" font-bold mb-4 text-2xl ">Tableau de présence</h2>
                <div id='calendar' class="my-10 lg:w-3/4 mx-auto"></div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const userId = {{ auth()->user()->id }};

            const statusColors = {
                present: '#22c55e',
                late: '#eab308',
                absent: '#ef4444'
            };

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour',
                    list: 'Liste'
                },
                events: buildEventsFromData(@json($daily_data)),

                eventClick: function(info) {
                    const status = info.event.extendedProps.status;

                    if (status === 'late' || status === 'present') {
                        const date = new Date(info.event.start);
                        const day = date.getDate();
                        const month = date.getMonth() + 1;
                        const year = date.getFullYear();

                        window.location.href =
                            `/attendances/${userId}?day=${day}&month=${month}&year=${year}`;
                    }
                },

                datesSet: function(info) {
                    const year = info.view.currentStart.getFullYear();
                    const month = info.view.currentStart.getMonth() + 1;

                    fetch(`/bilan/employee?year=${year}&month=${month}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            calendar.removeAllEvents();
                            calendar.addEventSource(buildEventsFromData(data.daily_data));
                        });

                }
            });

            calendar.render();

            // Convertit les données du back-end en événements FullCalendar
            function buildEventsFromData(statusMap) {
                return Object.entries(statusMap).map(([date, status]) => ({
                    title: (status === 'late') ? 'Retard' : status.charAt(0).toUpperCase() + status
                        .slice(1),
                    start: date,
                    allDay: true,
                    backgroundColor: statusColors[status],
                    borderColor: statusColors[status],
                    extendedProps: {
                        status: status
                    }
                }));
            }
        });
    </script>
</x-app-layout>
