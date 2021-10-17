<template>
    <div>
        <button type="button" class="btn btn-primary" @click="showModal = true">
            New Task
        </button>

        <div v-if="showModal" class="modal fade" :class="{'show': showModal}" style="display:block" tabindex="-1" aria-labelledby="task-create-title" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="task-create-title">New Task</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Title</label>
                        <input v-model="task.name" type="text" class="form-control" id="name">
                    </div>
                    <div class="mb-3">
                        <label for="start-date" class="form-label">Date</label>
                        <v-date-picker v-model="task.startDate" :popover="{visibility: 'focus'}" :min-date="new Date()" is-required>
                            <template v-slot="{ inputValue, inputEvents }">
                                <input type="text"
                                    class="form-control"
                                    id="start-date"
                                    :value="inputValue"
                                    v-on="inputEvents"
                                >
                            </template>
                        </v-date-picker>
                    </div>
                    <div class="mb-3">
                        <label for="repeat" class="form-label">Repeat</label>
                        <select v-model="task.repeat" class="form-control form-select" aria-label="Repeat" id="repeat">
                            <option value="never" selected>Never</option>
                            <option value="day">Daily</option>
                            <option value="week">Weekly</option>
                            <option value="month">Monthly</option>
                            <option value="year">Yearly</option>
                        </select>
                    </div>
                    <template v-if="task.repeat != 'never'">
                    <div v-if="task.repeat == 'week'" class="mb-3">
                    <div v-for="(week, index) in weekDays" :key="index" class="form-check">
                        <input v-model="task.weekDays" :disabled="index == getStartDay" :checked="index == getStartDay" class="form-check-input" type="checkbox" :value="week" :id="week">
                        <label class="form-check-label" :for="week">
                            {{ $_.capitalize(week) }}
                        </label>
                    </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="end-date">Ends</label>
                        <div class="row">
                            <div class="col-3">
                                <div class="form-check">
                                    <input v-model="task.ends" class="form-check-input" value="never" type="radio" name="ends" id="never-ending">
                                    <label class="form-check-label" for="never-ending">
                                        Never
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-3">
                                <div class="form-check">
                                    <input v-model="task.ends" class="form-check-input" value="on" type="radio" name="ends" id="on-ending">
                                    <label class="form-check-label" for="on-ending">
                                        On
                                    </label>
                                </div>
                            </div>
                            <div class="col-9">
                                <v-date-picker v-model="task.endDate" :popover="{visibility: 'focus'}" :min-date="task.startDate || new Date()">
                                    <template v-slot="{ inputValue, inputEvents }">
                                        <input type="text"
                                            class="form-control"
                                            id="end-date"
                                            :disabled="task.ends != 'on'"
                                            :value="inputValue"
                                            v-on="inputEvents"
                                        >
                                    </template>
                                </v-date-picker>
                            </div>
                        </div>
                    </div>
                    </template>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showModal = false">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="addTask">Save</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            showModal: false,
            task: {
                name: '',
                startDate: new Date(),
                repeat: 'never',
                weekDays: [],
                ends: 'never',
                endDate: null,
            },
            weekDays: [
                'sunday',
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
            ],
        }
    },
    computed: {
        getStartDay() {
            let startDay = this.task.startDate.getDay();

            // Quick Hack
            this.task.weekDays = [this.weekDays[startDay]];

            return startDay;
        }
    },
    methods: {
        addTask() {
            this.$http.post(process.env.MIX_APP_URL + '/tasks', this.formatTask())
                .then(response => {
                    this.showModal = false;
                }).catch(error => {
                    if (error.response.status == 422) {
                        alert('Please verify your input and try again.');
                        // Here we should inject the first error message under the respective inputs.
                    }
                });
        },
        formatTask() {
            let startDate = this.task.startDate?.toISOString();
            let endDate = this.task.endDate?.toISOString();

            return {
                name: this.task.name,
                start_date: startDate ? this.$_.truncate(startDate, {length: 10, omission: ''}) : null,
                repeat: this.task.repeat,
                week_days: this.task.weekDays,
                ends: this.task.ends,
                end_date: endDate ? this.$_.truncate(endDate, {length: 10, omission: ''}) : null,
            }
        }
    }
}
</script>

<style>

</style>