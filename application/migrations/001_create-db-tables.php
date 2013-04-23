<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Create_DB_Tables extends CI_Migration {

	public function up() {
		// TODO: Add foreign key constraints, unique constraints
		/* documents */
		$docFields = array(
				'id',
				'explicitId' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'file' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'title' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => TRUE),
				'authors' => array('type' => 'VARCHAR', 'constraint' => '300'),
				'publication' => array('type' => 'VARCHAR', 'constraint' => '200'),
				'editors' => array('type' => 'VARCHAR', 'constraint' => '300'),
				'publisher' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'year' => array('type' => 'INT', 'constraint' => '4'),
				'place' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'startPage' => array('type' => 'INT', 'constraint' => '5'),
				'endPage' => array('type' => 'INT', 'constraint' => '5'),
				'createdBy' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'scannedBy' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'managedBy' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'created' => array('type' => 'TIMESTAMP'),
				'lastEdited' => array('type' => 'TIMESTAMP')
			);
		$this->dbforge->add_field($docFields);
		$this->dbforge->create_table('documents', true));

		/* lists */
		$listFields = array(
				'id',
				'title' => array('type' => 'VARCHAR', 'constraint' => '200'),
				'createdBy' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'managedBy' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'created' => array('type' => 'TIMESTAMP'),
				'lastEdited' => array('type' => 'TIMESTAMP'),
				'editLock' => array('type' => 'VARCHAR', constraint = '100', 'null' => TRUE)
			);
		$this->dbforge->add_field($listFields);
		$this->dbforge->create_table('lists', true);

		/* documents2lists */
		$doc2listFields = array(
				'id',
				'documentId' => array('type' => 'VARCHAR', 'constraint' => '200'),
				'listId' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'created' => array('type' => 'TIMESTAMP'),
				'addedBy' => array('type' => 'VARCHAR', 'constraint' => '100'),
			);
		$this->dbforge->add_field($doc2listFields);
		$this->dbforge->create_table('documents2lists', true);

	}

	public function down() {
		$this->dbforge->drop_table('documents');
		$this->dbforge->drop_table('lists');
		$this->dbforge->drop_table('documents2lists');
	}	
}
