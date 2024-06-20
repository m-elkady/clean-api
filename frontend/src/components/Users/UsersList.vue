<template>
    <v-container>
        <v-row>
            <v-col cols="12"><v-card-title>Users</v-card-title></v-col>
        </v-row>
        <v-alert class="my-4" v-model="mainAlert.visible" :text="mainAlert.message" :type="mainAlert.type"
            closable></v-alert>
        <v-row class="my-4">
            <v-col cols="12">
                <v-btn color="primary" @click="goToCreate">Create New Item</v-btn>
            </v-col>
            <v-col cols="12">
                <v-row class="my-4" justify="center">
                    <v-col cols="3">
                        <v-text-field v-model="queryParams.firstName" label="Firstname"></v-text-field>
                    </v-col>
                    <v-col cols="3">
                        <v-text-field v-model="queryParams.lastName" label="Lastname"></v-text-field>
                    </v-col>
                    <v-col cols="3">
                        <v-text-field v-model="queryParams.userEmail" label="Email"></v-text-field>
                    </v-col>
                    <v-col cols="3">
                        <v-row>
                            <v-col>
                                <v-btn class="mr-2" color="primary" @click="search">Edit</v-btn>
                                <v-btn color="grey" @click="clear">Clear</v-btn>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="12">
                <v-table>
                    <thead>
                        <tr>
                            <th width="25%" class="text-left" @click="sortBy('firstName')" style="cursor: pointer;">
                                First Name <v-icon small> {{ sortIcon('firstName') }} </v-icon>
                            </th>
                            <th width="20%" class="text-left" @click="sortBy('lastName')" style="cursor: pointer;">
                                Last Name <v-icon small> {{ sortIcon('lastName') }} </v-icon>
                            </th>
                            <th width="20%" class="text-left" @click="sortBy('userEmail')" style="cursor: pointer;">
                                Email <v-icon small> {{ sortIcon('userEmail') }} </v-icon>
                            </th>
                            <th class="text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>{{ user.first_name }}</td>
                            <td>{{ user.last_name }}</td>
                            <td>{{ user.user_email }}</td>
                            <td>
                                <v-btn class="mr-2" color="warning" @click="goToEdit(user.id)">Edit</v-btn>

                                <v-btn color="error" @click="confirmDialogShow(user.id)">Delete</v-btn>
                            </td>
                        </tr>
                    </tbody>
                </v-table>
                <v-pagination v-model="pagination.page" :length="pagination.lenght" :total-visible="5"></v-pagination>
            </v-col>
        </v-row>
    </v-container>
    <!-- Create and Update Dialogue -->
    <v-dialog v-model="dialog.visible" width="auto">
        <v-card width="700" max-width="700" :prepend-icon="dialog.icon" :title="dialog.title">
            <v-form v-model="dialogValid" validate-on="submit blur" @submit.prevent="save">
                <v-card-text>
                    <v-text-field v-model="dialog.data.firstName" label="Firstname"
                        :rules="[rules.required]"></v-text-field>
                    <v-text-field v-model="dialog.data.lastName" label="Lastname"
                        :rules="[rules.required]"></v-text-field>
                    <v-text-field type="email" v-model="dialog.data.userEmail" label="Email"
                        :rules="[rules.required, rules.email]"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="green darken-1" type="submit" text>Save</v-btn>
                    <v-btn color="red darken-1" text @click="dialog.visible = false">Cancel</v-btn>
                </v-card-actions>
            </v-form>
        </v-card>

    </v-dialog>

    <v-dialog v-model="confirmDialog.visible" width="auto">
        <v-card dark width="300" max-width="300" prepend-icon="mdi-alert"
            text="are you sure you want to delete this user?">
            <template v-slot:title>
                <span class="font-weight-black">Alert</span>
            </template>

            <template v-slot:actions>
                <v-btn color="red darken-1" text @click="deleteUser(confirmDialog.id)">OK</v-btn>
                <v-btn text="cancel" @click="confirmDialog = false"></v-btn>
            </template>
        </v-card>

    </v-dialog>
</template>

<script>
import api from '@/services/axios';

const alertProps = { visible: false, message: '', type: 'info' };
const queryParams = JSON.parse(localStorage.getItem('queryParams')) || {
    page: 1,
    sortBy: 'id',
    order: 'asc',
    firstName: '',
    lastName: '',
    userEmail: '',
};
const dialogRules = {
    rules: {
        required: value => !!value || 'Field is required',
        email: v => !v || /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'E-mail must be valid'
    }
};
export default {
    data() {
        return {
            users: [],
            queryParams,
            dialogValid: false,
            confirmDialog: {
                visible: false,
                id: null
            },
            dialog: {
                visible: false,
                icon: 'mdi-plus',
                title: 'Create New User',
                data: {
                    id: '',
                    firstName: '',
                    lastName: '',
                    email: '',
                }
            },
            mainAlert: alertProps,
            modalAlert: alertProps,
            ...dialogRules,
            pagination: {
                page: 1,
                lenght: 1
            },
        };
    },
    watch: {
        pagination: {
            handler() {
                this.queryParams = { ...this.queryParams, ...{ page: this.pagination.page } };
                this.fetchUsers();
            },
            deep: true,
        },
        queryParams: {
            handler() {
                localStorage.setItem('queryParams', JSON.stringify(this.queryParams));
            }
        }
    },
    async created() {
        this.fetchUsers();
    },
    methods: {
        async fetchUsers() {
            try {
                const response = (await api.getUsers(this.queryParams)).data;
                this.users = response.users;
                const paginationLength = Math.ceil(response.count / response.limit);
                this.pagination.lenght = paginationLength;
            } catch (error) {
                console.error(error);
            }
        },
        goToCreate() {
            const userData = { id: '', firstName: '', lastName: '', userEmail: '' };
            this.dialog = { ...this.dialog, ...{ title: 'Create New User', icon: 'mdi-plus', visible: true, data: userData } }
        },
        async goToEdit(id) {
            const response = (await api.getUser(id)).data;
            const userData = { id: response.id, firstName: response.first_name, lastName: response.last_name, userEmail: response.user_email };
            this.dialog = { ...this.dialog, ...{ title: 'Edit User', icon: 'mdi-pencil', visible: true, data: userData } }
        },
        async save() {
            const id = this.dialog.data.id;

            if (!this.dialogValid) {
                return;
            }

            if (id !== '') {
                api.updateUser(id, this.dialog.data)
                    .then(response => {
                        this.mainAlert = { visible: true, message: response.data.message, type: 'success' };
                        this.dialog.visible = false;
                        this.fetchUsers();
                    });
            } else {
                api.createUser(this.dialog.data)
                    .then(response => {
                        this.mainAlert = { visible: true, message: response.data.message, type: 'success' };
                        this.dialog.visible = false;
                        this.fetchUsers();
                    });
            }

        },
        confirmDialogShow(id) {
            this.confirmDialog = { visible: true, id };
        },
        async deleteUser(id) {
            try {
                const { message, success } = (await api.deleteUser(id)).data;
                this.mainAlert = { visible: true, message, type: success ? 'success' : 'danger' };
                this.fetchUsers();
                this.confirmDialog.visible = false;
            } catch (error) {
                console.error(error);
            }
        },
        async search() {
            this.queryParams = { ...this.queryParams, ...{ page: 1 } };
            this.fetchUsers();
        },
        async clear() {
            this.queryParams = { ...this.queryParams, ...{ page: 1, firstName: '', lastName: '', userEmail: '' } };
            this.fetchUsers();
        },
        sortBy(key) {
            let order = this.queryParams.order;
            let sortBy = this.queryParams.sortBy;
            
            if (sortBy === key) {
                order = this.queryParams.order === 'asc' ? 'desc' : 'asc';
            } else {
                sortBy = key;
                order = 'asc';
            }

            this.queryParams =  { ...this.queryParams, ...{ order, sortBy } };
            this.fetchUsers();
        },
        sortIcon(key) {
            const sortBy = this.queryParams.sortBy;
 
            if (sortBy !== key) return 'mdi-sort';
            return this.queryParams.order === 'asc' ? 'mdi-arrow-up' : 'mdi-arrow-down';
        },
    },
};
</script>