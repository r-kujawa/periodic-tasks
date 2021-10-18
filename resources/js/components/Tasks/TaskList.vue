<template>
  <div class="container">
      <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                Tasks
                            </div>
                            <div>
                                <task-create />
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="mb-3 row align-items-center">
                            <div class="col-md-6">
                                <v-date-picker v-model="range" :popover="{visibility: 'focus'}" mode="date" is-range>
                                    <template v-slot="{ inputValue, inputEvents }">
                                        <div class="row">
                                            <label for="date_range_start" class="col-md-2 col-form-label text-end">From</label>
                                            <input type="text"
                                                class="form-control col-md-4"
                                                id="date_range_start"
                                                :value="inputValue.start"
                                                v-on="inputEvents.start"
                                            >
                                            <label for="date_range_end" class="col-md-2 col-form-label text-end">To</label>
                                            <input type="text"
                                                class="form-control col-md-4"
                                                id="date_range_end"
                                                :value="inputValue.end"
                                                v-on="inputEvents.end"
                                            >
                                        </div>
                                        
                                    </template>
                                </v-date-picker>
                            </div>
                            <div class="col-md-3 offset-md-3">
                                <div class="form-check">
                                    <input v-model="showCompleted" class="form-check-input" type="checkbox" :value="1" id="show_completed">
                                    <label class="form-check-label" for="show_completed">
                                        Show Completed
                                    </label>
                                </div>
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Task</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="date in dates">
                                <tr v-for="(task, index) in date.tasks" :key="date.date + '-' + task.id">
                                    <th scope="row">{{ ! index ? date.date : '' }}</th>
                                    <td><p :class="{'text-decoration-line-through': task.completed}">{{ task.name }}</p></td>
                                    <td><button v-if="!task.completed" @click="markCompleted(task, date.date)" type="button" class="btn btn-success btn-sm"><i class="bi-check-lg"></i></button></td>
                                </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
      
  </div>
</template>

<script>
export default {
    created() {
		this.fetchData();
    },
    data() {
        return {
            dates: [],
            showCompleted: 0,
            range: {
                start: new Date(),
                end: new Date(),
            }
        }
    },
    computed: {
        filters() {
            return {
                show_completed: this.showCompleted,
                from: this.range.start.toISOString().slice(0, 10),
                to: this.range.end.toISOString().slice(0, 10),
            };
        },
        query() {
            return '?' + Object.keys(this.filters).map(key => key + '=' + this.filters[key]).join('&');
        }
    },
    methods: {
        fetchData() {
            this.$http.get(process.env.MIX_APP_URL + '/api/tasks' + this.query)
                .then(response => {
                    this.dates = response.data;
                });
        },
        markCompleted(task, date) {
            this.$http.post(process.env.MIX_APP_URL + '/api/tasks/' + task.id + '/completed', {
                completion_date: date, 
            }).then(response => {
                this.fetchData();
            });
        }
    },
    watch: {
        filters: {
            deep: true,
            handler() {
                this.fetchData();
            }
        }
    }
}
</script>

<style>
.text-decoration-line-through{
    text-decoration: line-through;
}
</style>