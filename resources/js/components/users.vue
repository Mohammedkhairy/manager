<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdmin()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">All User Register</h3>

                <div class="card-tools">
                  <button class="btn btn-success" @click="openCreateModal()">Add New <i class="fas fa-user-plus fa-fw"></i></button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody><tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Register At</th>
                    <th>Status</th>
                    <th>Modify</th>
                  </tr>
                  <tr v-for="( user , index) in users.data" :key="user.id">
                     
                    <td>{{index+1}}</td>
                    <td>{{user.name}}</td>
                    <td>{{user.email}}</td>
                    <td>{{user.created_at | myDate}}</td>
                    <td><span class="tag tag-success">{{user.type | upText}}</span></td>
                    <td>
                        <a href="#" @click="openEditModal(user)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteUser(user.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                    </td> 
                  </tr>
                  
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="users" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>

         <div class="row mt-5" v-if="!$gate.isAdmin()">
              <not-found></not-found>
         </div>

        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered"  role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" v-show="editMode" id="addNewLabel">Update User Data</h5>
                <h5 class="modal-title" v-show="!editMode" id="addNewLabel">Add New User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form @submit.prevent=" editMode ? updateUser() : createUser()" >
            <div class="modal-body">
                    <div class="form-group">
                        <input v-model="form.name" type="text" name="name"
                            placeholder="Name"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                        <has-error :form="form" field="name"></has-error>
                    </div>

                     <div class="form-group">
                        <input v-model="form.email" type="email" name="email"
                            placeholder="Email Address"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                        <has-error :form="form" field="email"></has-error>
                    </div>

                     <div class="form-group">
                        <textarea v-model="form.bio" name="bio" id="bio"
                        placeholder="Short bio for user (Optional)"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                        <has-error :form="form" field="bio"></has-error>
                    </div>


                    <div class="form-group">
                        <select name="type" v-model="form.type" id="type" class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                            <option value="">Select User Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">Standard User</option>
                            <option value="author">Author</option>
                        </select>
                        <has-error :form="form" field="type"></has-error>
                    </div>

                    <div class="form-group">
                        <input v-model="form.password" type="password" name="password" id="password"
                        placeholder="Password must be great than  or equla 8 char"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                        <has-error :form="form" field="password"></has-error>
                    </div>
                

            </div>
          
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" v-show="editMode" class="btn btn-success">Update</button>
                <button type="submit" v-show="!editMode" class="btn btn-primary">Create</button>
            </div>
              </form>
            </div>

        </div>
        </div>
       
    </div>
</template>
<script>
    export default {
        data(){
            return{
                editMode:false,
                user:{},
                users:{},
                form:new Form({
                    id:'',
                    name : '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: ''
                })
            }
        },
        methods:{
            getResults(page = 1){
                axios.get("api/user?page="+page).then(response=>{
                    this.users = response.data;
                });
            },
            openEditModal(user){
                this.editMode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(user);

            },
            openCreateModal(){
                this.editMode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            loadUser(){
                if(this.$gate.isAdmin()){
                axios.get('api/user').then(( {data} ) => ( this.users = data));
                }
            },
            createUser(){
                this.$Progress.start();
                this.form.post('api/user')
                .then((response) =>{
                    this.user = response.data;
                    Fire.$emit("after_create");
                    $('#addNew').modal('hide');
                    this.form.reset();
                    Toast.fire({
                        type: 'success',
                        title: 'Created in successfully'
                    })
                    this.$Progress.finish();
                }).catch(() => {

                });
               
            },
            deleteUser(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.value) {
                            this.form.delete('api/user/'+id).then(()=>{
                                        Swal.fire(
                                            'Deleted!',
                                            'Your file has been deleted.',
                                            'success'
                                            )
                                        Fire.$emit('after_create');
                            
                            }).catch(()=> {
                                        Swal.fire("Failed!", "There was something wronge.", "warning");
                            });
                        }   
               });
            },
            updateUser(){
                this.$Progress.start();
                this.form.put('api/user/'+this.form.id).then(()=>{
                    Fire.$emit("after_create");
                    $('#addNew').modal('hide');
                    this.form.reset();
                    Toast.fire({
                        type: 'success',
                        title: 'Updated successfully'
                    })
                    Fire.$emit('after_create');
                    this.$Progress.finish();
                }).catch(() => {
                this.$Progress.fail();
                });
            
             }
        },     
        mounted() {
            Pusher.logToConsole = true;
                    var pusher = new Pusher('fe17f884326df2618ed8', {
                    cluster: 'eu',
                    encrypted: true
                    });
                    var channel = pusher.subscribe('update_user');
                    channel.bind('App\\Events\\update_user', function(data) {
                    // console.log(data);
                    Fire.$emit("after_create");
                    });
                    
            Fire.$on("searching",()=>{
                let q=this.$parent.search;
                axios.get('api/findUser?q='+q)
                .then((data)=>{
                    this.users=data.data;
                })
                .catch(()=>{

                })
            });
            this.loadUser();
            Fire.$on("after_create",()=>this.loadUser());
            
        }
    }
</script>
