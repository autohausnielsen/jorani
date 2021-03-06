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
 */

class Organization_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {

    }

    /**
     * Get the department details of a given user (label and ID)
     * @param int $user_id User identifier
     * @return array department details
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_department($user_id) {
        $this->db->select('organization.id as id, organization.name as name');
        $this->db->from('organization');
        $this->db->join('users', 'users.organization = organization.id');
        $this->db->where('users.id', $user_id);
        $query = $this->db->get();
        $arr = $query->result_array();
        return $arr;
    }
    
    /**
     * Get the label of a given entity id
     * @param type $id
     * @return string label
     */
    public function get_label($id) {
        $this->db->from('organization');
        $this->db->where("id", $id); 
        $query = $this->db->get();
        $record = $query->result_array();
        if(count($record) > 0) {
            return $record[0]['name'];
        } else {
            return '';
        }
    }
    
    /**
     * List all entities of the organisation
     * @return array all entities of the organization sorted out by id and name
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_all_entities() {
        $this->db->from('organization');
        $this->db->order_by("parent_id", "desc"); 
        $this->db->order_by("name", "asc");
        return $this->db->get();
    }

    /**
     * Get all children of an entity
     * @param int $id identifier of the entity
     * @return array list of entity identifiers
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function get_all_children($id) {
        $query = 'SELECT GetFamilyTree(id) as id' .
                    ' FROM organization' .
                    ' WHERE id =' . $id;
        $query = $this->db->query($query); 
        $arr = $query->result_array();
        return $arr;
    }
    
    /**
     * Move an entity into the organization
     * @param int $id identifier of the entity
     * @param type $parent_id new parent of the entity
     * @return type result of the query
     */
    public function move($id, $parent_id) {
        $data = array(
            'parent_id' => $parent_id
        );
        $this->db->where('id', $id);
        return $this->db->update('organization', $data);
    }
    
    /**
     * Add an employee into an entity of the organization
     * @param int $id identifier of the employee
     * @param int $entity identifier of the entity
     * @return type result of the query
     */
    public function add_employee($id, $entity) {
        $data = array(
            'organization' => $entity
        );
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    /**
     * Cascade delete children and set employees' org to NULL
     * @param int $entity identifier of the entity
     * @return type result of the query
     */
    public function delete($entity) {
        $list = $this->get_all_children($entity);
        //Detach all employees
        $data = array(
            'organization' => NULL
        );
        $ids = array();
        if (strlen($list[0]['id']) > 0) {
            $ids = explode(",", $list[0]['id']);
        }
        array_push($ids, $entity);
        $this->db->where_in('organization', $ids);
        $res1 = $this->db->update('users', $data);
        //Delete node and its children
        $this->db->where_in('id', $ids);
        $res2 = $this->db->delete('organization');
        return $res1 && $res2;
    }
    
    /**
     * Delete an employee from an entity of the organization
     * @param int $id identifier of the employee
     * @return type result of the query
     */
    public function delete_employee($id) {
        $data = array(
            'organization' => NULL
        );
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }
    
    /**
     * Rename an entity of the organization
     * @param int $id identifier of the entity
     * @param string $text new text of the entity
     * @return type result of the query
     */
    public function rename($id, $text) {
        $data = array(
            'name' => $text
        );
        $this->db->where('id', $id);
        return $this->db->update('organization', $data);
    }
    
    /**
     * Create an entity in the organization
     * @param int $parent_id identifier of the parent entity
     * @param string $text name of the new entity
     * @return type
     */
    public function create($parent_id, $text) {
        $data = array(
            'name' => $text,
            'parent_id' => $parent_id
        );
        return $this->db->insert('organization', $data);
    }
    
    /**
     * Copy an entity in the organization
     * @param int $id identifier of the source entity
     * @param int $parent_id identifier of the new parent entity
     * @return type
     */
    public function copy($id, $parent_id) {
        $this->db->from('organization');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $row = $query->row();
        $data = array(
            'name' => $row->name,
            'parent_id' => $parent_id
        );
        return $this->db->insert('organization', $data);
    }
    
    /**
     * Returns the list of the employees attached to an entity
     * @param int $id identifier of the entity
     * @return type
     */
    public function employees($id) {
        $this->db->select('id, firstname, lastname, email');
        $this->db->from('users');
        $this->db->where('organization', $id);
        $this->db->order_by('lastname', 'asc'); 
        $this->db->order_by('firstname', 'asc');
        return $this->db->get();
    }
    
    /**
     * Returns the list of the employees attached to an entity
     * @param int $id identifier of the entity
     * @param bool $children Include sub department in the query
     * @return type
     */
    public function all_employees($id, $children = false) {
        $this->db->select('users.id, users.identifier, users.firstname, users.lastname, users.datehired');
        $this->db->select('organization.name as department, positions.name as position, contracts.name as contract');
        $this->db->from('organization');
        $this->db->join('users', 'users.organization = organization.id');
        $this->db->join('positions', 'positions.id  = users.position', 'left');
        $this->db->join('contracts', 'contracts.id  = users.contract', 'left');
        if ($children == true) {
            $this->load->model('organization_model');
            $list = $this->organization_model->get_all_children($id);
            $ids = array();
            if ($list[0]['id'] != '') {
                $ids = explode(",", $list[0]['id']);
                array_push($ids, $id);
                $this->db->where_in('organization.id', $ids);
            } else {
                $this->db->where('organization.id', $id);
            }
        } else {
            $this->db->where('organization.id', $id);
        }
        $this->db->order_by('lastname', 'asc'); 
        $this->db->order_by('firstname', 'asc');
        $employees = $this->db->get()->result();
        return $employees;
    }
    
    /**
     * Add an employee into an entity of the organization
     * @param int $id identifier of the employee
     * @param int $entity identifier of the entity
     * @return int result of the query
     */
    public function set_supervisor($id, $entity) {
        $data = array(
            'supervisor' => $id
        );
        $this->db->where('id', $entity);
        return $this->db->update('organization', $data);
    }
    
    /**
     * Returns the supervisor of an entity
     * @param int $entity identifier of the entity
     * @return object identifier of supervisor
     */
    public function get_supervisor($entity) {
        $this->db->select('users.id, CONCAT(users.firstname, \' \', users.lastname) as username, email', FALSE);
        $this->db->from('organization');
        $this->db->join('users', 'users.id = organization.supervisor');
        $this->db->where('organization.id', $entity);
        $result = $this->db->get()->result();
        if (count($result) > 0) {
            return $result[0];
        } else {
            return NULL;
        }
    }
}
