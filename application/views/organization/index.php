<?php 
/*
 * This file is part of Jorani.
 *
 * Jorani is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jorani is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jorani.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @copyright  Copyright (c) 2014 - 2015 Benjamin BALET
 */
?>

<h1><?php echo lang('organization_index_title');?> &nbsp;<?php echo $help;?></h1>

<div class="row-fluid">
    <div class="span4">
        <div class="input-append">
            <input type="text" class="input-medium" placeholder="<?php echo lang('organization_index_field_search_placeholder');?>" id="txtSearch" />
            <button id="cmdClearSearch" class="btn btn-primary"><i class="icon-remove icon-white"></i></button>
            <button id="cmdSearch" class="btn btn-primary"><i class="icon-search icon-white"></i>&nbsp;<?php echo lang('organization_index_button_search');?></button>
        </div>
        <div style="text-align: left;" id="organization"></div>
    </div>
    <div class="span8">
        <h3><?php echo lang('organization_index_title_employees');?></h3>
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="collaborators" width="100%">
            <thead>
                <tr>
                    <th><?php echo lang('organization_index_thead_id');?></th>
                    <th><?php echo lang('organization_index_thead_firstname');?></th>
                    <th><?php echo lang('organization_index_thead_lastname');?></th>
                    <th><?php echo lang('organization_index_thead_email');?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <br />
        <button id="cmdAddEmployee" class="btn btn-primary"><?php echo lang('organization_index_button_add_employee');?></button>
        <button id="cmdRemoveEmployee" class="btn btn-primary"><?php echo lang('organization_index_button_remove_employee');?></button>
        <br />
        <h3><?php echo lang('organization_index_title_supervisor');?></h3>
        <p><?php echo lang('organization_index_description_supervisor');?></p>
        <div class="input-append">
            <input type="text" id="txtSupervisor" />
            <button id="cmdDeleteSupervisor" class="btn btn-danger"><i class="icon-remove icon-white"></i></button>
            <button id="cmdSelectSupervisor" class="btn btn-primary"><?php echo lang('organization_index_button_select_supervisor');?></button>
        </div>
        <br /><br />
    </div>
</div>

<style>
    tr.row_selected td{background-color:#b0bed9 !important;}
</style>

<div id="frmConfirmDelete" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmConfirmDelete').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('organization_index_popup_delete_title');?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo lang('organization_index_popup_delete_description');?></p>
        <p><?php echo lang('organization_index_popup_delete_confirm');?></p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn danger" id="lnkDeleteEntity"><?php echo lang('organization_index_popup_delete_button_yes');?></a>
        <a href="#" onclick="$('#organization').jstree('refresh'); $('#frmConfirmDelete').modal('hide');" class="btn secondary"><?php echo lang('organization_index_popup_delete_button_no');?></a>
    </div>
</div>

<div id="frmAddEmployee" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmAddEmployee').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('organization_index_popup_add_title');?></h3>
    </div>
    <div class="modal-body" id="frmAddEmployeeBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="add_employee();" class="btn secondary"><?php echo lang('organization_index_popup_add_button_ok');?></a>
        <a href="#" onclick="$('#frmAddEmployee').modal('hide');" class="btn secondary"><?php echo lang('organization_index_popup_add_button_cancel');?></a>
    </div>
</div>

<div id="frmSelectSupervisor" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectSupervisor').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('organization_index_popup_supervisor_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectSupervisorBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_supervisor();" class="btn secondary"><?php echo lang('organization_index_popup_supervisor_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectSupervisor').modal('hide');" class="btn secondary"><?php echo lang('organization_index_popup_supervisor_button_cancel');?></a>
    </div>
</div>

<div id="frmError" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmError').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('organization_index_popup_error_title');?></h3>
    </div>
    <div class="modal-body" id="lblError"></div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmError').modal('hide');" class="btn secondary"><?php echo lang('organization_index_popup_error_button_ok');?></a>
    </div>
</div>

<div class="modal hide" id="frmModalAjaxWait" data-backdrop="static" data-keyboard="false">
        <div class="modal-header">
            <h1><?php echo lang('global_msg_wait');?></h1>
        </div>
        <div class="modal-body">
            <img src="<?php echo base_url();?>assets/images/loading.gif"  align="middle">
        </div>
 </div>

<link href="<?php echo base_url();?>assets/datatable/css/jquery.dataTables.css" rel="stylesheet">
<link rel="stylesheet" href='<?php echo base_url(); ?>assets/jsTree/themes/default/style.css' type="text/css" media="screen, projection" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/jsTree/jstree.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>

<script type="text/javascript">
    //In order to manipulate datable object
    var oTable;
    //Mutex to prevent rename the root node
    var createMtx = false;
    
    function add_employee() {
        var id = $('#employees .row_selected td:first').text();
        var entity = $('#organization').jstree('get_selected')[0];
        $.ajax({
            type: "GET",
            url: "<?php echo base_url(); ?>organization/addemployee",
            data: { 'user': id, 'entity': entity }
          })
          .done(function(msg) {
            //Update table of users
            $('#frmModalAjaxWait').modal('show');
            oTable.ajax.url("<?php echo base_url(); ?>organization/employees?id=" + entity)
            .load(function() {
                    $("#frmModalAjaxWait").modal('hide');
                }, true);
            $("#frmAddEmployee").modal('hide');
          });
    }
    
    function select_supervisor() {
        $("#frmSelectSupervisor").modal('hide');
        $('#frmModalAjaxWait').modal('show');
        var id = $('#employees .row_selected td:first').text();
        var entity = $('#organization').jstree('get_selected')[0];
        var text = $('#employees .row_selected td:eq(1)').text();
        text += ' ' + $('#employees .row_selected td:eq(2)').text();
        $.ajax({
            type: "GET",
            url: "<?php echo base_url(); ?>organization/setsupervisor",
            data: { 'user': id, 'entity': entity }
          })
          .done(function(msg) {
            //Update field with the name of employee (the supervisor)
            $('#txtSupervisor').val(text);
            $('#frmModalAjaxWait').modal('hide');
          });
    }
    
    function delete_supervisor() {
        $('#frmModalAjaxWait').modal('show');
        var entity = $('#organization').jstree('get_selected')[0];
        $.ajax({
            type: "GET",
            url: "<?php echo base_url(); ?>organization/setsupervisor",
            data: { 'user': null, 'entity': entity }
          })
          .done(function(msg) {
            //Update field with the name of employee (the supervisor)
            $('#txtSupervisor').val("");
            $('#frmModalAjaxWait').modal('hide');
          });
    }
    
    $(function () {
        //On confirm the deletion of the node, launch heavy cascade deletion
        $("#lnkDeleteEntity").click(function() {
            $.ajax({
                type: "GET",
                url: "<?php echo base_url(); ?>organization/delete",
                data: { 'entity': $('#frmConfirmDelete').data('id') }
              })
              .done(function(msg) {
                $("#organization").jstree("select_node", "0"); 
                $("#organization").jstree("refresh");
                $("#frmConfirmDelete").modal('hide');
              });
        });
       
        //Attach an employee to an entity
        $("#cmdAddEmployee").click(function() {
            if ($("#organization").jstree('get_selected').length == 1) {
                $("#frmAddEmployee").modal('show');
                $("#frmAddEmployeeBody").load('<?php echo base_url(); ?>users/employees');
            } else {
                $("#lblError").text("<?php echo lang('organization_index_error_msg_select_entity');?>");
                $("#frmError").modal('show');
            }
        });

        //Select the supervisor of the entity
        $("#cmdSelectSupervisor").click(function() {
            if ($("#organization").jstree('get_selected').length == 1) {
                $("#frmSelectSupervisor").modal('show');
                $("#frmSelectSupervisorBody").load('<?php echo base_url(); ?>users/employees');
            } else {
                $("#lblError").text("<?php echo lang('organization_index_error_msg_select_entity');?>");
                $("#frmError").modal('show');
            }
        });
        
        //Delete the supervisor of the entity
        $("#cmdDeleteSupervisor").click(function() {
            if ($("#organization").jstree('get_selected').length == 1) {
                delete_supervisor();
            } else {
                $("#lblError").text("<?php echo lang('organization_index_error_msg_select_entity');?>");
                $("#frmError").modal('show');
            }
        });

        //Remove an employee to an entity
        $("#cmdRemoveEmployee").click(function() {
            var id = $('#collaborators .row_selected td:first').text();
            if (id != "") {
                if ($("#organization").jstree('get_selected').length == 1) {
                    var entity = $('#organization').jstree('get_selected')[0];
                    $.ajax({
                        type: "GET",
                        url: "<?php echo base_url(); ?>organization/delemployee",
                        data: { 'user': id }
                      })
                      .done(function( msg ) {
                        //Update table of users
                        $('#frmModalAjaxWait').modal('show');
                        oTable.ajax.url("<?php echo base_url(); ?>organization/employees?id=" + entity)
                        .load(function() {
                                $("#frmModalAjaxWait").modal('hide');
                            }, true);
                    });
                } else {
                    $("#lblError").text("<?php echo lang('organization_index_error_msg_select_entity');?>");
                    $("#frmError").modal('show');
                }
            } else {
                $("#lblError").text("<?php echo lang('organization_index_error_msg_select_employee');?>");
                $("#frmError").modal('show');
                $("#frmErrorEmployee").modal('show');
            }
        });

        //Load alert forms
        $("#frmAddEmployee").alert();
        //Prevent to load always the same content (refreshed each time)
        $('#frmAddEmployee').on('hidden', function() {
            $( "#employees" ).remove();
            $(this).removeData('modal');
        });
        $('#frmSelectSupervisor').on('hidden', function() {
            $( "#employees" ).remove();
            $(this).removeData('modal');
        });
        
        //Search in the treeview
        $("#cmdSearch").click(function () {
            $("#organization").jstree("search", $("#txtSearch").val(), true, true);
        });
        $("#txtSearch").keyup(function(e) {
            if (e.keyCode == 13) { $("#organization").jstree("search", $("#txtSearch").val(), true, true); }   // enter key
        });
        
        //Clear the Search option in the treeview
        $("#cmdClearSearch").click(function () {
            $("#organization").jstree("clear_search");
        });
        $(document).keyup(function(e) {
            if (e.keyCode == 27) { $("#organization").jstree("clear_search"); }   // escape key
        });
        
        
        //Transform the HTML table in a fancy datatable
        oTable = $('#collaborators').DataTable({
            fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                //As the datatable is populated with Ajax we need to add a callback this way
                $('td', nRow).on('click', function() {
                    $("#collaborators tbody tr").removeClass('row_selected');
                    $(nRow).addClass("row_selected");
                });
            },
            "oLanguage": {
                    "sEmptyTable":     "<?php echo lang('datatable_sEmptyTable');?>",
                    "sInfo":           "<?php echo lang('datatable_sInfo');?>",
                    "sInfoEmpty":      "<?php echo lang('datatable_sInfoEmpty');?>",
                    "sInfoFiltered":   "<?php echo lang('datatable_sInfoFiltered');?>",
                    "sInfoPostFix":    "<?php echo lang('datatable_sInfoPostFix');?>",
                    "sInfoThousands":  "<?php echo lang('datatable_sInfoThousands');?>",
                    "sLengthMenu":     "<?php echo lang('datatable_sLengthMenu');?>",
                    "sLoadingRecords": "<?php echo lang('datatable_sLoadingRecords');?>",
                    "sProcessing":     "<?php echo lang('datatable_sProcessing');?>",
                    "sSearch":         "<?php echo lang('datatable_sSearch');?>",
                    "sZeroRecords":    "<?php echo lang('datatable_sZeroRecords');?>",
                    "oPaginate": {
                        "sFirst":    "<?php echo lang('datatable_sFirst');?>",
                        "sLast":     "<?php echo lang('datatable_sLast');?>",
                        "sNext":     "<?php echo lang('datatable_sNext');?>",
                        "sPrevious": "<?php echo lang('datatable_sPrevious');?>"
                    },
                    "oAria": {
                        "sSortAscending":  "<?php echo lang('datatable_sSortAscending');?>",
                        "sSortDescending": "<?php echo lang('datatable_sSortDescending');?>"
                    }
                }
        });
        
        //Initialize the tree of the organization
        $('#organization').jstree({
            contextmenu: {
                items: function(n) {
                    var tmp = $.jstree.defaults.contextmenu.items();
                    tmp.create.label = '<?php echo lang('treeview_context_menu_create');?>';
                    tmp.rename.label = '<?php echo lang('treeview_context_menu_rename');?>';
                    tmp.remove.label = '<?php echo lang('treeview_context_menu_remove');?>';
                    tmp.ccp.label = '<?php echo lang('treeview_context_menu_edit');?>';
                    tmp.ccp.submenu.copy.label = '<?php echo lang('treeview_context_menu_copy');?>';
                    tmp.ccp.submenu.cut.label = '<?php echo lang('treeview_context_menu_cut');?>';
                    tmp.ccp.submenu.paste.label = '<?php echo lang('treeview_context_menu_paste');?>';
                    return tmp;
                }
            },
            rules: {
                deletable  : [ "folder" ],
                creatable  : [ "folder" ],
                draggable  : [ "folder" ],
                dragrules  : [ "folder * folder", "folder inside root" ],
                renameable : "all"
              },
            core: {
              multiple : false,
              data: {
                url: function (node) {
                  return node.id === '#' ? 
                    '<?php echo base_url(); ?>organization/root' : 
                    '<?php echo base_url(); ?>organization/children';
                },
                data: function (node) {
                  return { 'id' : node.id };
                }
              },
              check_callback : true
            },
            plugins: ["contextmenu", "dnd", "search", "state", "sort", "unique"]
        })
        .on('delete_node.jstree', function (e, data) {
            var id = data.node.id;
            if (id == 0) {
                $("#lblError").text("<?php echo lang('organization_index_error_msg_delete_root');?>");
                $("#frmError").modal('show');
                $("#organization").jstree("refresh");
            } else {
                $('#frmConfirmDelete').data('id', id).modal('show');
            }
        })
        .on('create_node.jstree', function (e, data) {
            createMtx = true;
            bootbox.prompt("<?php echo lang('organization_index_prompt_entity_name');?>",
                "<?php echo lang('organization_index_popup_node_button_cancel');?>",
                "<?php echo lang('organization_index_popup_node_button_ok');?>", function(result) {
                if (result === null) {
                    data.instance.refresh();
                } else {
                    $.get('organization/create', { 'id' : data.node.parent, 'position' : data.position, 'text' : result })
                    .done(function (d) {
                        data.instance.set_id(data.node, d.id);
                        createMtx = false;
                    })
                    .fail(function() {
                        data.instance.refresh();
                        createMtx = false;
                    });
                }
              });
        })
        .on('rename_node.jstree', function(e, data) {
            if (!createMtx) {
                $.get('organization/rename', {'id': data.node.id, 'text': data.text})
                    .fail(function() {
                        data.instance.refresh();
                    });
            }
        })
        .on('move_node.jstree', function(e, data) {
            e.preventDefault();
            $.get('organization/move', {'id': data.node.id, 'parent': data.parent, 'position': data.position})
                .fail(function() {
                    data.instance.refresh();
                });
        })
        .on('copy_node.jstree', function(e, data) {
            e.preventDefault();
            $.get('organization/copy', {'id': data.original.id, 'parent': data.parent, 'position': data.position})
                .always(function() {
                    data.instance.refresh();
                });
        })
        .on('changed.jstree', function(e, data) {
            if (data && data.selected && data.selected.length) {
                $('#frmModalAjaxWait').modal('show');
                var isTableLoaded = false;
                var isSupervisorLoaded = false;
                oTable.ajax.url("<?php echo base_url(); ?>organization/employees?id=" + data.selected.join(':'))
                    .load(function() {
                            isTableLoaded = true;
                        }, true);
                $.ajax({
                    type: "GET",
                    url: "<?php echo base_url(); ?>organization/getsupervisor",
                        data: { 'entity': data.selected.join(':') }
                      })
                    .done(function(data) {
                        //Update field with the name of employee (the supervisor)
                        if (data != null && typeof data === 'object') {
                            $('#txtSupervisor').val(data.username);
                        } else {
                            $('#txtSupervisor').val("");
                        }
                        isSupervisorLoaded = true;
                        $.when(isTableLoaded, isTableLoaded).done(function() { 
                            $("#frmModalAjaxWait").modal('hide');
                        });
                  });
            }
        });
    });
</script>
