<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Create_Db_Tables extends CI_Migration {

	public function up() {
		// DONE: Add foreign key constraints, unique constraints
		
		/* documents */
		$this->dbforge->add_field('id');
		// $this->dbforge->add_key('id', true);
		$docFields = array(
				'explicitId' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'fileName' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'title' => array('type' => 'VARCHAR', 'constraint' => '255'),
				'authors' => array('type' => 'VARCHAR', 'constraint' => '255'),
				'publication' => array('type' => 'VARCHAR', 'constraint' => '200'),
				'editors' => array('type' => 'VARCHAR', 'constraint' => '255'),
				'publisher' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'year' => array('type' => 'INT', 'constraint' => '4'),
				'place' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'startPage' => array('type' => 'INT', 'constraint' => '5'),
				'endPage' => array('type' => 'INT', 'constraint' => '5'),
				'createdBy' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'scannedBy' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'managedBy' => array('type' => 'VARCHAR', 'constraint' => '100'),
				// lastUpdated has to come before created,
				// so that the "on update CURRENT_TIMESTAMP" constraint is used here
				'lastUpdated' => array('type' => 'TIMESTAMP'),	
				'created' => array('type' => 'TIMESTAMP'),
			);
		$this->dbforge->add_field($docFields);
		$this->dbforge->create_table('documents', true);
	
		/* lists */
		$this->dbforge->add_field('id');
		// $this->dbforge->add_key('id', true);
		$listFields = array(
				'title' => array('type' => 'VARCHAR', 'constraint' => '255'),
				'createdBy' => array('type' => 'VARCHAR', 'constraint' => '100'),
				'managedBy' => array('type' => 'VARCHAR', 'constraint' => '100'),
				// lastUpdated has to come before created,
				// so that the "on update CURRENT_TIMESTAMP" constraint is used here
				'lastUpdated' => array('type' => 'TIMESTAMP'),
				'created' => array('type' => 'TIMESTAMP')
			);
		$this->dbforge->add_field($listFields);
		$this->dbforge->create_table('lists', true);

		/* documents2lists */
		$this->dbforge->add_field('id');
		// $this->dbforge->add_key('id', true);
		$doc2listFields = array(
				'documentId' => array('type' => 'INT', 'constraint' => '9'),
				'listId' => array('type' => 'INT', 'constraint' => '9'),
				// lastUpdated has to come before created,
				// so that the "on update CURRENT_TIMESTAMP" constraint is used here
				'lastUpdated' => array('type' => 'TIMESTAMP'),
				'created' => array('type' => 'TIMESTAMP'),
				'addedBy' => array('type' => 'VARCHAR', 'constraint' => '100'),
			);
		$this->dbforge->add_field($doc2listFields);
		$this->dbforge->create_table('documents2lists', true);

		/* get tableNames with prefixes */
		$docsTableWPrfx = $this->db->dbprefix('documents'); // get the tableName with prefix
		$listsTableWPrfx = $this->db->dbprefix('lists'); // get the tableName with prefix
		$doc2listTableWPrfx = $this->db->dbprefix('documents2lists'); // get the tableName with prefix

		/* change engines to InnoDB and add constraints */
		$this->db->query("ALTER TABLE `$docsTableWPrfx` ENGINE=InnoDB, ADD CONSTRAINT UNIQUE (`explicitId`)");
		$this->db->query("ALTER TABLE `$listsTableWPrfx` ENGINE=InnoDB");
		$this->db->query("ALTER TABLE `$doc2listTableWPrfx` ENGINE=InnoDB, ADD CONSTRAINT FOREIGN KEY (`documentId`) REFERENCES `$docsTableWPrfx` (`id`)");
		$this->db->query("ALTER TABLE `$doc2listTableWPrfx` ENGINE=InnoDB, ADD CONSTRAINT FOREIGN KEY (`listId`) REFERENCES `$listsTableWPrfx` (`id`)");

	}

	public function down() {
		$this->dbforge->drop_table('documents');
		$this->dbforge->drop_table('lists');
		$this->dbforge->drop_table('documents2lists');
	}	
}
