<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2015                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */


require_once 'CiviTest/CiviUnitTestCase.php';

/**
 * Class CRM_Core_BAO_ActionScheduleTest
 */
class CRM_Core_BAO_ActionScheduleTest extends CiviUnitTestCase {

  /**
   * @var object see CiviTest/CiviMailUtils
   */
  public $mut;

  public function setUp() {
    parent::setUp();

    require_once 'CiviTest/CiviMailUtils.php';
    $this->mut = new CiviMailUtils($this, TRUE);

    $this->fixtures['rolling_membership'] = array(
      'membership_type_id' => array(
        'period_type' => 'rolling',
        'duration_unit' => 'month',
        'duration_interval' => '3',
        'is_active' => 1,
      ),
      'join_date' => '20120315',
      'start_date' => '20120315',
      'end_date' => '20120615',
      'is_override' => 0,
    );

    $this->fixtures['rolling_membership_past'] = array(
      'membership_type_id' => array(
        'period_type' => 'rolling',
        'duration_unit' => 'month',
        'duration_interval' => '3',
        'is_active' => 1,
      ),
      'join_date' => '20100310',
      'start_date' => '20100310',
      'end_date' => '20100610',
      'is_override' => 'NULL',
    );

    $this->fixtures['phonecall'] = array(
      'status_id' => 1,
      'activity_type_id' => 2,
      'activity_date_time' => '20120615100000',
      'is_current_revision' => 1,
      'is_deleted' => 0,
    );
    $this->fixtures['contact'] = array(
      'is_deceased' => 0,
      'contact_type' => 'Individual',
      'email' => 'test-member@example.com',
    );
    $this->fixtures['contact_birthdate'] = array(
      'is_deceased' => 0,
      'contact_type' => 'Individual',
      'email' => 'test-bday@example.com',
      'birth_date' => '20050707',
    );
    $this->fixtures['sched_activity_1day'] = array(
      'name' => 'One_Day_Phone_Call_Notice',
      'title' => 'One Day Phone Call Notice',
      'limit_to' => '1',
      'absolute_date' => NULL,
      'body_html' => '<p>1-Day (non-repeating)</p>',
      'body_text' => '1-Day (non-repeating)',
      'end_action' => NULL,
      'end_date' => NULL,
      'end_frequency_interval' => NULL,
      'end_frequency_unit' => NULL,
      'entity_status' => '1',
      'entity_value' => '2',
      'group_id' => NULL,
      'is_active' => '1',
      'is_repeat' => '0',
      'mapping_id' => '1',
      'msg_template_id' => NULL,
      'recipient' => '2',
      'recipient_listing' => NULL,
      'recipient_manual' => NULL,
      'record_activity' => NULL,
      'repetition_frequency_interval' => NULL,
      'repetition_frequency_unit' => NULL,
      'start_action_condition' => 'before',
      'start_action_date' => 'activity_date_time',
      'start_action_offset' => '1',
      'start_action_unit' => 'day',
      'subject' => '1-Day (non-repeating)',
    );
    $this->fixtures['sched_activity_1day_r'] = array(
      'name' => 'One_Day_Phone_Call_Notice_R',
      'title' => 'One Day Phone Call Notice R',
      'limit_to' => 1,
      'absolute_date' => NULL,
      'body_html' => '<p>1-Day (repeating)</p>',
      'body_text' => '1-Day (repeating)',
      'end_action' => 'after',
      'end_date' => 'activity_date_time',
      'end_frequency_interval' => '2',
      'end_frequency_unit' => 'day',
      'entity_status' => '1',
      'entity_value' => '2',
      'group_id' => NULL,
      'is_active' => '1',
      'is_repeat' => '1',
      'mapping_id' => '1',
      'msg_template_id' => NULL,
      'recipient' => '2',
      'recipient_listing' => NULL,
      'recipient_manual' => NULL,
      'record_activity' => NULL,
      'repetition_frequency_interval' => '6',
      'repetition_frequency_unit' => 'hour',
      'start_action_condition' => 'before',
      'start_action_date' => 'activity_date_time',
      'start_action_offset' => '1',
      'start_action_unit' => 'day',
      'subject' => '1-Day (repeating)',
    );
    $this->fixtures['sched_membership_join_2week'] = array(
      'name' => 'sched_membership_join_2week',
      'title' => 'sched_membership_join_2week',
      'absolute_date' => '',
      'body_html' => '<p>body sched_membership_join_2week</p>',
      'body_text' => 'body sched_membership_join_2week',
      'end_action' => '',
      'end_date' => '',
      'end_frequency_interval' => '',
      'end_frequency_unit' => '',
      'entity_status' => '',
      'entity_value' => '',
      'group_id' => '',
      'is_active' => 1,
      'is_repeat' => '0',
      'mapping_id' => 4,
      'msg_template_id' => '',
      'recipient' => '',
      'recipient_listing' => '',
      'recipient_manual' => '',
      'record_activity' => 1,
      'repetition_frequency_interval' => '',
      'repetition_frequency_unit' => '',
      'start_action_condition' => 'after',
      'start_action_date' => 'membership_join_date',
      'start_action_offset' => '2',
      'start_action_unit' => 'week',
      'subject' => 'subject sched_membership_join_2week',
    );
    $this->fixtures['sched_membership_end_2week'] = array(
      'name' => 'sched_membership_end_2week',
      'title' => 'sched_membership_end_2week',
      'absolute_date' => '',
      'body_html' => '<p>body sched_membership_end_2week</p>',
      'body_text' => 'body sched_membership_end_2week',
      'end_action' => '',
      'end_date' => '',
      'end_frequency_interval' => '',
      'end_frequency_unit' => '',
      'entity_status' => '',
      'entity_value' => '',
      'group_id' => '',
      'is_active' => 1,
      'is_repeat' => '0',
      'mapping_id' => 4,
      'msg_template_id' => '',
      'recipient' => '',
      'recipient_listing' => '',
      'recipient_manual' => '',
      'record_activity' => 1,
      'repetition_frequency_interval' => '',
      'repetition_frequency_unit' => '',
      'start_action_condition' => 'before',
      'start_action_date' => 'membership_end_date',
      'start_action_offset' => '2',
      'start_action_unit' => 'week',
      'subject' => 'subject sched_membership_end_2week',
    );
    $this->fixtures['sched_on_membership_end_date'] = array(
      'name' => 'sched_on_membership_end_date',
      'title' => 'sched_on_membership_end_date',
      'body_html' => '<p>Your membership expired today</p>',
      'body_text' => 'Your membership expired today',
      'is_active' => 1,
      'mapping_id' => 4,
      'record_activity' => 1,
      'start_action_condition' => 'after',
      'start_action_date' => 'membership_end_date',
      'start_action_offset' => '0',
      'start_action_unit' => 'hour',
      'subject' => 'subject send reminder on membership_end_date',
    );
    $this->fixtures['sched_after_1day_membership_end_date'] = array(
      'name' => 'sched_after_1day_membership_end_date',
      'title' => 'sched_after_1day_membership_end_date',
      'body_html' => '<p>Your membership expired yesterday</p>',
      'body_text' => 'Your membership expired yesterday',
      'is_active' => 1,
      'mapping_id' => 4,
      'record_activity' => 1,
      'start_action_condition' => 'after',
      'start_action_date' => 'membership_end_date',
      'start_action_offset' => '1',
      'start_action_unit' => 'day',
      'subject' => 'subject send reminder on membership_end_date',
    );

    $this->fixtures['sched_membership_end_2month'] = array(
      'name' => 'sched_membership_end_2month',
      'title' => 'sched_membership_end_2month',
      'absolute_date' => '',
      'body_html' => '<p>body sched_membership_end_2month</p>',
      'body_text' => 'body sched_membership_end_2month',
      'end_action' => '',
      'end_date' => '',
      'end_frequency_interval' => '',
      'end_frequency_unit' => '',
      'entity_status' => '',
      'entity_value' => '',
      'group_id' => '',
      'is_active' => 1,
      'is_repeat' => '0',
      'mapping_id' => 4,
      'msg_template_id' => '',
      'recipient' => '',
      'recipient_listing' => '',
      'recipient_manual' => '',
      'record_activity' => 1,
      'repetition_frequency_interval' => '',
      'repetition_frequency_unit' => '',
      'start_action_condition' => 'after',
      'start_action_date' => 'membership_end_date',
      'start_action_offset' => '2',
      'start_action_unit' => 'month',
      'subject' => 'subject sched_membership_end_2month',
    );

    $this->fixtures['sched_contact_bday_yesterday'] = array(
      'name' => 'sched_contact_bday_yesterday',
      'title' => 'sched_contact_bday_yesterday',
      'absolute_date' => '',
      'body_html' => '<p>you look like you were born yesterday!</p>',
      'body_text' => 'you look like you were born yesterday!',
      'end_action' => '',
      'end_date' => '',
      'end_frequency_interval' => '',
      'end_frequency_unit' => '',
      'entity_status' => 1,
      'entity_value' => 'birth_date',
      'group_id' => '',
      'is_active' => 1,
      'is_repeat' => '0',
      'mapping_id' => 6,
      'msg_template_id' => '',
      'recipient' => '',
      'recipient_listing' => '',
      'recipient_manual' => '',
      'record_activity' => 1,
      'repetition_frequency_interval' => '',
      'repetition_frequency_unit' => '',
      'start_action_condition' => 'after',
      'start_action_date' => 'date_field',
      'start_action_offset' => '1',
      'start_action_unit' => 'day',
      'subject' => 'subject sched_contact_bday_yesterday',
    );

    $this->fixtures['sched_contact_bday_anniv'] = array(
      'name' => 'sched_contact_bday_anniv',
      'title' => 'sched_contact_bday_anniv',
      'absolute_date' => '',
      'body_html' => '<p>happy birthday!</p>',
      'body_text' => 'happy birthday!',
      'end_action' => '',
      'end_date' => '',
      'end_frequency_interval' => '',
      'end_frequency_unit' => '',
      'entity_status' => 2,
      'entity_value' => 'birth_date',
      'group_id' => '',
      'is_active' => 1,
      'is_repeat' => '0',
      'mapping_id' => 6,
      'msg_template_id' => '',
      'recipient' => '',
      'recipient_listing' => '',
      'recipient_manual' => '',
      'record_activity' => 1,
      'repetition_frequency_interval' => '',
      'repetition_frequency_unit' => '',
      'start_action_condition' => 'before',
      'start_action_date' => 'date_field',
      'start_action_offset' => '1',
      'start_action_unit' => 'day',
      'subject' => 'subject sched_contact_bday_anniv',
    );

    $this->fixtures['sched_contact_grad_tomorrow'] = array(
      'name' => 'sched_contact_grad_tomorrow',
      'title' => 'sched_contact_grad_tomorrow',
      'absolute_date' => '',
      'body_html' => '<p>congratulations on your graduation!</p>',
      'body_text' => 'congratulations on your graduation!',
      'end_action' => '',
      'end_date' => '',
      'end_frequency_interval' => '',
      'end_frequency_unit' => '',
      'entity_status' => 1,
      'group_id' => '',
      'is_active' => 1,
      'is_repeat' => '0',
      'mapping_id' => 6,
      'msg_template_id' => '',
      'recipient' => '',
      'recipient_listing' => '',
      'recipient_manual' => '',
      'record_activity' => 1,
      'repetition_frequency_interval' => '',
      'repetition_frequency_unit' => '',
      'start_action_condition' => 'before',
      'start_action_date' => 'date_field',
      'start_action_offset' => '1',
      'start_action_unit' => 'day',
      'subject' => 'subject sched_contact_grad_tomorrow',
    );

    $this->fixtures['sched_contact_grad_anniv'] = array(
      'name' => 'sched_contact_grad_anniv',
      'title' => 'sched_contact_grad_anniv',
      'absolute_date' => '',
      'body_html' => '<p>dear alum, please send us money.</p>',
      'body_text' => 'dear alum, please send us money.',
      'end_action' => '',
      'end_date' => '',
      'end_frequency_interval' => '',
      'end_frequency_unit' => '',
      'entity_status' => 2,
      'group_id' => '',
      'is_active' => 1,
      'is_repeat' => '0',
      'mapping_id' => 6,
      'msg_template_id' => '',
      'recipient' => '',
      'recipient_listing' => '',
      'recipient_manual' => '',
      'record_activity' => 1,
      'repetition_frequency_interval' => '',
      'repetition_frequency_unit' => '',
      'start_action_condition' => 'after',
      'start_action_date' => 'date_field',
      'start_action_offset' => '1',
      'start_action_unit' => 'week',
      'subject' => 'subject sched_contact_grad_anniv',
    );

    $this->fixtures['sched_contact_created_yesterday'] = array(
      'name' => 'sched_contact_created_yesterday',
      'title' => 'sched_contact_created_yesterday',
      'absolute_date' => '',
      'body_html' => '<p>Your contact was created yesterday</p>',
      'body_text' => 'Your contact was created yesterday!',
      'end_action' => '',
      'end_date' => '',
      'end_frequency_interval' => '',
      'end_frequency_unit' => '',
      'entity_status' => 1,
      'entity_value' => 'created_date',
      'group_id' => '',
      'is_active' => 1,
      'is_repeat' => '0',
      'mapping_id' => 6,
      'msg_template_id' => '',
      'recipient' => '',
      'recipient_listing' => '',
      'recipient_manual' => '',
      'record_activity' => 1,
      'repetition_frequency_interval' => '',
      'repetition_frequency_unit' => '',
      'start_action_condition' => 'after',
      'start_action_date' => 'date_field',
      'start_action_offset' => '1',
      'start_action_unit' => 'day',
      'subject' => 'subject sched_contact_created_yesterday',
    );

    $this->fixtures['sched_contact_mod_anniv'] = array(
      'name' => 'sched_contact_mod_anniv',
      'title' => 'sched_contact_mod_anniv',
      'absolute_date' => '',
      'body_html' => '<p>You last updated your data last year</p>',
      'body_text' => 'Go update your stuff!',
      'end_action' => '',
      'end_date' => '',
      'end_frequency_interval' => '',
      'end_frequency_unit' => '',
      'entity_status' => 2,
      'entity_value' => 'modified_date',
      'group_id' => '',
      'is_active' => 1,
      'is_repeat' => '0',
      'mapping_id' => 6,
      'msg_template_id' => '',
      'recipient' => '',
      'recipient_listing' => '',
      'recipient_manual' => '',
      'record_activity' => 1,
      'repetition_frequency_interval' => '',
      'repetition_frequency_unit' => '',
      'start_action_condition' => 'before',
      'start_action_date' => 'date_field',
      'start_action_offset' => '1',
      'start_action_unit' => 'day',
      'subject' => 'subject sched_contact_mod_anniv',
    );

    $this->fixtures['sched_membership_end_2month_repeat_twice_4_weeks'] = array(
      'name' => 'sched_membership_end_2month',
      'title' => 'sched_membership_end_2month',
      'absolute_date' => '',
      'body_html' => '<p>body sched_membership_end_2month</p>',
      'body_text' => 'body sched_membership_end_2month',
      'end_action' => '',
      'end_date' => 'membership_end_date',
      'end_frequency_interval' => '4',
      'end_frequency_unit' => 'month',
      'entity_status' => '',
      'entity_value' => '',
      'group_id' => '',
      'is_active' => 1,
      'is_repeat' => '1',
      'mapping_id' => 4,
      'msg_template_id' => '',
      'recipient' => '',
      'recipient_listing' => '',
      'recipient_manual' => '',
      'record_activity' => 1,
      'repetition_frequency_interval' => '4',
      'repetition_frequency_unit' => 'week',
      'start_action_condition' => 'after',
      'start_action_date' => 'membership_end_date',
      'start_action_offset' => '2',
      'start_action_unit' => 'month',
      'subject' => 'subject sched_membership_end_2month',
    );
    $this->fixtures['sched_membership_end_limit_to_none'] = array(
      'name' => 'limit to none',
      'title' => 'limit to none',
      'absolute_date' => '',
      'body_html' => '<p>body sched_membership_end_2month</p>',
      'body_text' => 'body sched_membership_end_2month',
      'end_action' => '',
      'end_date' => '',
      'end_frequency_interval' => '4',
      'end_frequency_unit' => 'month',
      'entity_status' => '',
      'entity_value' => '',
      'limit_to' => 0,
      'group_id' => '',
      'is_active' => 1,
      'is_repeat' => '1',
      'mapping_id' => 4,
      'msg_template_id' => '',
      'recipient' => '',
      'recipient_listing' => '',
      'recipient_manual' => '',
      'record_activity' => 1,
      'repetition_frequency_interval' => '4',
      'repetition_frequency_unit' => 'week',
      'start_action_condition' => 'after',
      'start_action_date' => 'membership_end_date',
      'start_action_offset' => '2',
      'start_action_unit' => 'month',
      'subject' => 'limit to none',
    );
    $this->_setUp();
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   *
   * This method is called after a test is executed.
   */
  public function tearDown() {
    parent::tearDown();

    $this->mut->clearMessages();
    $this->mut->stop();
    unset($this->mut);
    $this->quickCleanup(array(
      'civicrm_action_schedule',
      'civicrm_action_log',
      'civicrm_membership',
      'civicrm_email',
    ));
    $this->_tearDown();
  }

  public function testActivityDateTimeMatchNonRepeatableSchedule() {
    $actionScheduleDao = CRM_Core_BAO_ActionSchedule::add($this->fixtures['sched_activity_1day']);
    $this->assertTrue(is_numeric($actionScheduleDao->id));

    $activity = $this->createTestObject('CRM_Activity_DAO_Activity', $this->fixtures['phonecall']);
    $this->assertTrue(is_numeric($activity->id));
    $contact = $this->callAPISuccess('contact', 'create', $this->fixtures['contact']);
    $activity->save();

    $source['contact_id'] = $contact['id'];
    $source['activity_id'] = $activity->id;
    $source['record_type_id'] = 2;
    $activityContact = $this->createTestObject('CRM_Activity_DAO_ActivityContact', $source);
    $activityContact->save();

    $this->assertCronRuns(array(
      array(
        // Before the 24-hour mark, no email
        'time' => '2012-06-14 04:00:00',
        'recipients' => array(),
      ),
      array(
        // After the 24-hour mark, an email
        'time' => '2012-06-14 15:00:00',
        'recipients' => array(array('test-member@example.com')),
      ),
      array(
        // Run cron again; message already sent
        'time' => '',
        'recipients' => array(),
      ),
    ));
  }

  public function testActivityDateTimeMatchRepeatableSchedule() {
    $actionScheduleDao = CRM_Core_BAO_ActionSchedule::add($this->fixtures['sched_activity_1day_r']);
    $this->assertTrue(is_numeric($actionScheduleDao->id));

    $activity = $this->createTestObject('CRM_Activity_DAO_Activity', $this->fixtures['phonecall']);
    $this->assertTrue(is_numeric($activity->id));
    $contact = $this->callAPISuccess('contact', 'create', $this->fixtures['contact']);
    $activity->save();

    $source['contact_id'] = $contact['id'];
    $source['activity_id'] = $activity->id;
    $source['record_type_id'] = 2;
    $activityContact = $this->createTestObject('CRM_Activity_DAO_ActivityContact', $source);
    $activityContact->save();

    $this->assertCronRuns(array(
      array(
        // Before the 24-hour mark, no email
        'time' => '012-06-14 04:00:00',
        'recipients' => array(),
      ),
      array(
        // After the 24-hour mark, an email
        'time' => '2012-06-14 15:00:00',
        'recipients' => array(array('test-member@example.com')),
      ),
      array(
        // Run cron 4 hours later; first message already sent
        'time' => '2012-06-14 20:00:00',
        'recipients' => array(),
      ),
      array(
        // Run cron 6 hours later; send second message.
        'time' => '2012-06-14 21:00:01',
        'recipients' => array(array('test-member@example.com')),
      ),
    ));
  }

  /**
   * For contacts/activities which don't match the schedule filter,
   * an email should *not* be sent.
   */
  // TODO // function testActivityDateTime_NonMatch() { }

  /**
   * For contacts/members which match schedule based on join date,
   * an email should be sent.
   */
  public function testMembershipJoinDateMatch() {
    $membership = $this->createTestObject('CRM_Member_DAO_Membership', array_merge($this->fixtures['rolling_membership'], array('status_id' => 1)));
    $this->assertTrue(is_numeric($membership->id));
    $result = $this->callAPISuccess('Email', 'create', array(
      'contact_id' => $membership->contact_id,
      'email' => 'test-member@example.com',
      'location_type_id' => 1,
    ));
    $this->assertAPISuccess($result);

    $this->callAPISuccess('contact', 'create', array_merge($this->fixtures['contact'], array('contact_id' => $membership->contact_id)));
    $actionSchedule = $this->fixtures['sched_membership_join_2week'];
    $actionSchedule['entity_value'] = $membership->membership_type_id;
    $actionScheduleDao = CRM_Core_BAO_ActionSchedule::add($actionSchedule);
    $this->assertTrue(is_numeric($actionScheduleDao->id));

    // start_date=2012-03-15 ; schedule is 2 weeks after start_date
    $this->assertCronRuns(array(
      array(
        // Before the 2-week mark, no email.
        'time' => '2012-03-28 01:00:00',
        'recipients' => array(),
      ),
      array(
        // After the 2-week mark, send an email.
        'time' => '2012-03-29 01:00:00',
        'recipients' => array(array('test-member@example.com')),
      ),
    ));
  }

  /**
   * Test end date email sent.
   *
   * For contacts/members which match schedule based on join date,
   * an email should be sent.
   */
  public function testMembershipJoinDateNonMatch() {
    $membership = $this->createTestObject('CRM_Member_DAO_Membership', $this->fixtures['rolling_membership']);
    $this->assertTrue(is_numeric($membership->id));
    $result = $this->callAPISuccess('Email', 'create', array(
      'contact_id' => $membership->contact_id,
      'location_type_id' => 1,
      'email' => 'test-member@example.com',
    ));

    // Add an alternative membership type, and only send messages for that type
    $extraMembershipType = $this->createTestObject('CRM_Member_DAO_MembershipType', array());
    $this->assertTrue(is_numeric($extraMembershipType->id));
    $actionScheduleDao = CRM_Core_BAO_ActionSchedule::add($this->fixtures['sched_membership_join_2week']);
    $this->assertTrue(is_numeric($actionScheduleDao->id));
    $actionScheduleDao->entity_value = $extraMembershipType->id;
    $actionScheduleDao->save();

    // start_date=2012-03-15 ; schedule is 2 weeks after start_date
    $this->assertCronRuns(array(
      array(
        // After the 2-week mark, don't send email because we have different membership type.
        'time' => '2012-03-29 01:00:00',
        'recipients' => array(),
      ),
    ));
  }

  /**
   * Test that the first and SECOND notifications are sent out.
   */
  public function testMembershipEndDateRepeat() {
    // creates membership with end_date = 20120615
    $membership = $this->createTestObject('CRM_Member_DAO_Membership', array_merge($this->fixtures['rolling_membership'], array('status_id' => 2)));
    $result = $this->callAPISuccess('Email', 'create', array(
      'contact_id' => $membership->contact_id,
      'email' => 'test-member@example.com',
    ));
    $this->callAPISuccess('contact', 'create', array_merge($this->fixtures['contact'], array('contact_id' => $membership->contact_id)));

    $actionSchedule = $this->fixtures['sched_membership_end_2month_repeat_twice_4_weeks'];
    $actionSchedule['entity_value'] = $membership->membership_type_id;
    $this->callAPISuccess('action_schedule', 'create', $actionSchedule);

    // end_date=2012-06-15 ; schedule is 2 weeks before end_date
    $this->assertCronRuns(array(
      array(
        // After the 2-week mark, send an email.
        'time' => '2012-08-15 01:00:00',
        'recipients' => array(array('test-member@example.com')),
      ),
      array(
        // After the 2-week mark, send an email.
        'time' => '2012-09-12 01:00:00',
        'recipients' => array(array('test-member@example.com')),
      ),
    ));
  }

  /**
   * Test behaviour when date changes.
   *
   * Test that the first notification is sent but the second is NOT sent if the end date changes in
   * between
   *  see CRM-15376
   */
  public function testMembershipEndDateRepeatChangedEndDate_CRM_15376() {
    // creates membership with end_date = 20120615
    $membership = $this->createTestObject('CRM_Member_DAO_Membership', array_merge($this->fixtures['rolling_membership'], array('status_id' => 2)));
    $this->callAPISuccess('Email', 'create', array(
      'contact_id' => $membership->contact_id,
      'email' => 'test-member@example.com',
    ));
    $this->callAPISuccess('contact', 'create', array_merge($this->fixtures['contact'], array('contact_id' => $membership->contact_id)));

    $actionSchedule = $this->fixtures['sched_membership_end_2month_repeat_twice_4_weeks'];
    $actionSchedule['entity_value'] = $membership->membership_type_id;
    $this->callAPISuccess('action_schedule', 'create', $actionSchedule);
    // end_date=2012-06-15 ; schedule is 2 weeks before end_date
    $this->assertCronRuns(array(
      array(
        // After the 2-week mark, send an email.
        'time' => '2012-08-15 01:00:00',
        'recipients' => array(array('test-member@example.com')),
      ),
    ));

    // Extend membership - reminder should NOT go out.
    $this->callAPISuccess('membership', 'create', array('id' => $membership->id, 'end_date' => '2014-01-01'));
    $this->assertCronRuns(array(
      array(
        // After the 2-week mark, send an email.
        'time' => '2012-09-12 01:00:00',
        'recipients' => array(),
      ),
    ));
  }

  /**
   * Test membership end date email sends.
   *
   * For contacts/members which match schedule based on end date,
   * an email should be sent.
   */
  public function testMembershipEndDateMatch() {
    // creates membership with end_date = 20120615
    $membership = $this->createTestObject('CRM_Member_DAO_Membership', array_merge($this->fixtures['rolling_membership'], array('status_id' => 2)));
    $this->assertTrue(is_numeric($membership->id));
    $this->callAPISuccess('Email', 'create', array(
      'contact_id' => $membership->contact_id,
      'email' => 'test-member@example.com',
    ));
    $this->callAPISuccess('contact', 'create', array_merge($this->fixtures['contact'], array('contact_id' => $membership->contact_id)));

    $actionSchedule = $this->fixtures['sched_membership_end_2week'];
    $actionSchedule['entity_value'] = $membership->membership_type_id;
    $actionScheduleDao = CRM_Core_BAO_ActionSchedule::add($actionSchedule);
    $this->assertTrue(is_numeric($actionScheduleDao->id));

    // end_date=2012-06-15 ; schedule is 2 weeks before end_date
    $this->assertCronRuns(array(
      array(
        // Before the 2-week mark, no email.
        'time' => '2012-05-31 01:00:00',
        // 'time' => '2012-06-01 01:00:00',
        // FIXME: Is this the right boundary?
        'recipients' => array(),
      ),
      array(
        // After the 2-week mark, send an email.
        'time' => '2012-06-01 01:00:00',
        'recipients' => array(array('test-member@example.com')),
      ),
    ));

    // Now suppose user has renewed for rolling membership after 3 months, so upcoming assertion is written
    // to ensure that new reminder is sent 2 week before the new end_date i.e. '2012-09-15'
    $membership->end_date = '2012-09-15';
    $membership->save();

    //change the email id of chosen membership contact to assert
    //recipient of not the previously sent mail but the new one
    $result = $this->callAPISuccess('Email', 'create', array(
      'is_primary' => 1,
      'contact_id' => $membership->contact_id,
      'email' => 'member2@example.com',
    ));
    $this->assertAPISuccess($result);

    // end_date=2012-09-15 ; schedule is 2 weeks before end_date
    $this->assertCronRuns(array(
      array(
        // Before the 2-week mark, no email
        'time' => '2012-08-31 01:00:00',
        'recipients' => array(),
      ),
      //array( // After the 2-week mark, send an email
      //'time' => '2012-09-01 01:00:00',
      //'recipients' => array(array('member2@example.com')),
      //),
    ));
  }


  /**
   * Test membership end date email.
   *
   * For contacts/members which match schedule based on end date,
   * an email should be sent.
   */
  public function testMembershipEndDateNoMatch() {
    // creates membership with end_date = 20120615
    $membership = $this->createTestObject('CRM_Member_DAO_Membership', array_merge($this->fixtures['rolling_membership'], array('status_id' => 3)));
    $this->assertTrue(is_numeric($membership->id));
    $result = $this->callAPISuccess('Email', 'create', array(
      'contact_id' => $membership->contact_id,
      'email' => 'test-member@example.com',
    ));
    $this->callAPISuccess('contact', 'create', array_merge($this->fixtures['contact'], array('contact_id' => $membership->contact_id)));

    $actionSchedule = $this->fixtures['sched_membership_end_2month'];
    $actionSchedule['entity_value'] = $membership->membership_type_id;
    $actionScheduleDao = CRM_Core_BAO_ActionSchedule::add($actionSchedule);
    $this->assertTrue(is_numeric($actionScheduleDao->id));

    // end_date=2012-06-15 ; schedule is 2 weeks before end_date
    $this->assertCronRuns(array(
      array(
        // Before the 2-week mark, no email.
        'time' => '2012-05-31 01:00:00',
        // 'time' => '2012-06-01 01:00:00',
        // FIXME: Is this the right boundary?
        'recipients' => array(),
      ),
      array(
        // After the 2-week mark, send an email.
        'time' => '2013-05-01 01:00:00',
        'recipients' => array(),
      ),
    ));
  }

  public function testContactBirthDateNoAnniv() {
    $contact = $this->callAPISuccess('Contact', 'create', $this->fixtures['contact_birthdate']);
    $this->_testObjects['CRM_Contact_DAO_Contact'][] = $contact['id'];
    $actionSchedule = $this->fixtures['sched_contact_bday_yesterday'];
    $actionScheduleDao = CRM_Core_BAO_ActionSchedule::add($actionSchedule);
    $this->assertTrue(is_numeric($actionScheduleDao->id));
    $this->assertCronRuns(array(
      array(
        // On the birthday, no email.
        'time' => '2005-07-07 01:00:00',
        'recipients' => array(),
      ),
      array(
        // The next day, send an email.
        'time' => '2005-07-08 20:00:00',
        'recipients' => array(array('test-bday@example.com')),
      ),
    ));
  }

  public function testContactBirthDateAnniversary() {
    $contact = $this->callAPISuccess('Contact', 'create', $this->fixtures['contact_birthdate']);
    $this->_testObjects['CRM_Contact_DAO_Contact'][] = $contact['id'];
    $actionSchedule = $this->fixtures['sched_contact_bday_anniv'];
    $actionScheduleDao = CRM_Core_BAO_ActionSchedule::add($actionSchedule);
    $this->assertTrue(is_numeric($actionScheduleDao->id));
    $this->assertCronRuns(array(
      array(
        // On some random day, no email.
        'time' => '2014-03-07 01:00:00',
        'recipients' => array(),
      ),
      array(
        // On the eve of their 9th birthday, send an email.
        'time' => '2014-07-06 20:00:00',
        'recipients' => array(array('test-bday@example.com')),
      ),
    ));
  }

  public function testContactCustomDateNoAnniv() {
    $group = array(
      'title' => 'Test_Group',
      'name' => 'test_group',
      'extends' => array('Individual'),
      'style' => 'Inline',
      'is_multiple' => FALSE,
      'is_active' => 1,
    );
    $createGroup = $this->callAPISuccess('custom_group', 'create', $group);
    $field = array(
      'label' => 'Graduation',
      'data_type' => 'Date',
      'html_type' => 'Select Date',
      'custom_group_id' => $createGroup['id'],
    );
    $createField = $this->callAPISuccess('custom_field', 'create', $field);
    $contactParams = $this->fixtures['contact'];
    $contactParams["custom_{$createField['id']}"] = '2013-12-16';
    $contact = $this->callAPISuccess('Contact', 'create', $contactParams);
    $this->_testObjects['CRM_Contact_DAO_Contact'][] = $contact['id'];
    $actionSchedule = $this->fixtures['sched_contact_grad_tomorrow'];
    $actionSchedule['entity_value'] = "custom_{$createField['id']}";
    $actionScheduleDao = CRM_Core_BAO_ActionSchedule::add($actionSchedule);
    $this->assertTrue(is_numeric($actionScheduleDao->id));
    $this->assertCronRuns(array(
      array(
        // On some random day, no email.
        'time' => '2014-03-07 01:00:00',
        'recipients' => array(),
      ),
      array(
        // On the eve of their graduation, send an email.
        'time' => '2013-12-15 20:00:00',
        'recipients' => array(array('test-member@example.com')),
      ),
    ));
    $this->callAPISuccess('custom_group', 'delete', array('id' => $createGroup['id']));
  }

  public function testContactCreatedNoAnniv() {
    $contact = $this->callAPISuccess('Contact', 'create', $this->fixtures['contact_birthdate']);
    $this->_testObjects['CRM_Contact_DAO_Contact'][] = $contact['id'];
    $actionSchedule = $this->fixtures['sched_contact_created_yesterday'];
    $actionScheduleDao = CRM_Core_BAO_ActionSchedule::add($actionSchedule);
    $this->assertTrue(is_numeric($actionScheduleDao->id));
    $this->assertCronRuns(array(
      array(
        // On the date created, no email.
        'time' => $contact['values'][$contact['id']]['created_date'],
        'recipients' => array(),
      ),
      array(
        // The next day, send an email.
        'time' => date('Y-m-d H:i:s', strtotime($contact['values'][$contact['id']]['created_date'] . ' +1 day')),
        'recipients' => array(array('test-bday@example.com')),
      ),
    ));
  }

  public function testContactModifiedAnniversary() {
    $contact = $this->callAPISuccess('Contact', 'create', $this->fixtures['contact_birthdate']);
    $this->_testObjects['CRM_Contact_DAO_Contact'][] = $contact['id'];
    $actionSchedule = $this->fixtures['sched_contact_mod_anniv'];
    $actionScheduleDao = CRM_Core_BAO_ActionSchedule::add($actionSchedule);
    $this->assertTrue(is_numeric($actionScheduleDao->id));
    $this->assertCronRuns(array(
      array(
        // On some random day, no email.
        'time' => '2014-03-07 01:00:00',
        'recipients' => array(),
      ),
      array(
        // On the eve of 3 years after they were modified, send an email.
        'time' => date('Y-m-d H:i:s', strtotime($contact['values'][$contact['id']]['modified_date'] . ' +3 years -1 day')),
        'recipients' => array(array('test-bday@example.com')),
      ),
    ));
  }

  /**
   * Check that limit_to + an empty recipients doesn't sent to multiple contacts.
   */
  public function testMembershipLimitToNone() {
    // creates membership with end_date = 20120615
    $membership = $this->createTestObject('CRM_Member_DAO_Membership', array_merge($this->fixtures['rolling_membership'], array('status_id' => 2)));

    $this->assertTrue(is_numeric($membership->id));
    $result = $this->callAPISuccess('Email', 'create', array(
      'contact_id' => $membership->contact_id,
      'email' => 'member@example.com',
    ));
    $this->callAPISuccess('contact', 'create', array_merge($this->fixtures['contact'], array('contact_id' => $membership->contact_id)));
    $this->callAPISuccess('contact', 'create', array('email' => 'b@c.com', 'contact_type' => 'Individual'));

    $this->assertAPISuccess($result);

    $actionSchedule = $this->fixtures['sched_membership_end_limit_to_none'];
    $actionSchedule['entity_value'] = $membership->membership_type_id;
    $actionScheduleDao = CRM_Core_BAO_ActionSchedule::add($actionSchedule);
    $this->assertTrue(is_numeric($actionScheduleDao->id));

    // end_date=2012-06-15 ; schedule is 2 weeks before end_date
    $this->assertCronRuns(array(
      array(
        // Before the 2-week mark, no email.
        'time' => '2012-05-31 01:00:00',
        // 'time' => '2012-06-01 01:00:00', // FIXME: Is this the right boundary?
        'recipients' => array(),
      ),
    ));
  }

  public function testMembership_referenceDate() {
    $membership = $this->createTestObject('CRM_Member_DAO_Membership', array_merge($this->fixtures['rolling_membership'], array('status_id' => 2)));

    $this->assertTrue(is_numeric($membership->id));
    $this->callAPISuccess('Email', 'create', array(
        'contact_id' => $membership->contact_id,
        'email' => 'member@example.com',
    ));

    $result = $this->callAPISuccess('contact', 'create', array_merge($this->fixtures['contact'], array('contact_id' => $membership->contact_id)));
    $this->assertAPISuccess($result);

    $actionSchedule = $this->fixtures['sched_membership_join_2week'];
    $actionSchedule['entity_value'] = $membership->membership_type_id;
    $actionScheduleDao = CRM_Core_BAO_ActionSchedule::add($actionSchedule);
    $this->assertTrue(is_numeric($actionScheduleDao->id));

    // start_date=2012-03-15 ; schedule is 2 weeks after start_date
    $this->assertCronRuns(array(
      array(
        // After the 2-week mark, send an email
        'time' => '2012-03-29 01:00:00',
        'recipients' => array(array('member@example.com')),
      ),
      array(
        // After the 2-week 1day mark, don't send an email
        'time' => '2012-03-30 01:00:00',
        'recipients' => array(),
      ),
    ));

    //check if reference date is set to membership's join date
    //as per the action_start_date chosen for current schedule reminder
    $this->assertEquals('2012-03-15',
      CRM_Core_DAO::getFieldValue('CRM_Core_DAO_ActionLog', $membership->contact_id, 'reference_date', 'contact_id')
    );

    //change current membership join date that may signifies as memberhip renewal activity
    $membership->join_date = '2012-03-29';
    $membership->save();

    $this->assertCronRuns(array(
      array(
        // After the 13 days of the changed join date 2012-03-29, don't send an email
        'time' => '2012-04-11 01:00:00',
        'recipients' => array(),
      ),
      array(
         // After the 2-week of the changed join date 2012-03-29, send an email
        'time' => '2012-04-12 01:00:00',
        'recipients' => array(array('member@example.com')),
      ),
    ));
    $this->assertCronRuns(array(
        array(
          // It should not re-send on the same day
          'time' => '2012-04-12 01:00:00',
          'recipients' => array(),
        ),
    ));
  }

  public function testMembershipOnMultipleReminder() {
    $membership = $this->createTestObject('CRM_Member_DAO_Membership', array_merge($this->fixtures['rolling_membership'], array('status_id' => 2)));

    $this->assertTrue(is_numeric($membership->id));
    $result = $this->callAPISuccess('Email', 'create', array(
      'contact_id' => $membership->contact_id,
      'email' => 'member@example.com',
    ));
    $result = $this->callAPISuccess('contact', 'create', array_merge($this->fixtures['contact'], array('contact_id' => $membership->contact_id)));
    $this->assertAPISuccess($result);

    $actionScheduleBefore = $this->fixtures['sched_membership_end_2week'];           // Send email 2 weeks before end_date
    $actionScheduleOn = $this->fixtures['sched_on_membership_end_date'];             // Send email on end_date/expiry date
    $actionScheduleAfter = $this->fixtures['sched_after_1day_membership_end_date'];  // Send email 1 day after end_date/grace period
    $actionScheduleBefore['entity_value'] = $actionScheduleOn['entity_value'] = $actionScheduleAfter['entity_value'] = $membership->membership_type_id;
    foreach (array('actionScheduleBefore', 'actionScheduleOn', 'actionScheduleAfter') as $value) {
      $$value = CRM_Core_BAO_ActionSchedule::add($$value);
      $this->assertTrue(is_numeric($$value->id));
    }

    $this->assertCronRuns(
      array(
        array(
          // 1day 2weeks before membership end date(MED), don't send mail
          'time' => '2012-05-31 01:00:00',
          'recipients' => array(),
        ),
        array(
          // 2 weeks before MED, send an email
          'time' => '2012-06-01 01:00:00',
          'recipients' => array(array('member@example.com')),
        ),
        array(
          // 1day before MED, don't send mail
          'time' => '2012-06-14 01:00:00',
          'recipients' => array(),
        ),
        array(
          // On MED, send an email
          'time' => '2012-06-15 00:00:00',
          'recipients' => array(array('member@example.com')),
        ),
        array(
          // After 1day of MED, send an email
          'time' => '2012-06-16 01:00:00',
          'recipients' => array(array('member@example.com')),
        ),
        array(
          // After 1day 1min of MED, don't send an email
          'time' => '2012-06-17 00:01:00',
          'recipients' => array(),
        ),
      )
    );

    // Assert the timestamp as of when the emails of respective three reminders as configured
    // 2 weeks before, on and 1 day after MED, are sent
    $this->assertEquals('2012-06-01 01:00:00',
      CRM_Core_DAO::getFieldValue('CRM_Core_DAO_ActionLog', $actionScheduleBefore->id, 'action_date_time', 'action_schedule_id', TRUE));
    $this->assertEquals('2012-06-15 00:00:00',
      CRM_Core_DAO::getFieldValue('CRM_Core_DAO_ActionLog', $actionScheduleOn->id, 'action_date_time', 'action_schedule_id', TRUE));
    $this->assertEquals('2012-06-16 01:00:00',
      CRM_Core_DAO::getFieldValue('CRM_Core_DAO_ActionLog', $actionScheduleAfter->id, 'action_date_time', 'action_schedule_id', TRUE));

    //extend MED to 2 weeks after the current MED (that may signifies as memberhip renewal activity)
    // and lets assert as of when the new set of reminders will be sent against their respective Schedule Reminders(SR)
    $membership->end_date = '2012-06-20';
    $membership->save();

    $result = $this->callAPISuccess('Contact', 'get', array('id' => $membership->contact_id));
    $this->assertCronRuns(
      array(
        array(
          // 1day 2weeks before membership end date(MED), don't send mail
          'time' => '2012-06-05 01:00:00',
          'recipients' => array(),
        ),
        array(
          // 2 weeks before MED, send an email
          'time' => '2012-06-06 01:00:00',
          'recipients' => array(array('member@example.com')),
        ),
        array(
          // 1day before MED, don't send mail
          'time' => '2012-06-19 01:00:00',
          'recipients' => array(),
        ),
        array(
          // On MED, send an email
          'time' => '2012-06-20 00:00:00',
          'recipients' => array(array('member@example.com')),
        ),
        array(
          // After 1day of MED, send an email
          'time' => '2012-06-21 01:00:00',
          'recipients' => array(array('member@example.com')),
        ),
        array(
          // After 1day 1min of MED, don't send an email
          'time' => '2012-07-21 00:01:00',
          'recipients' => array(),
        ),
      ));
  }

  public function testContactCustomDate_Anniv() {
    $group = array(
      'title' => 'Test_Group now',
      'name' => 'test_group_now',
      'extends' => array('Individual'),
      'style' => 'Inline',
      'is_multiple' => FALSE,
      'is_active' => 1,
    );
    $createGroup = $this->callAPISuccess('custom_group', 'create', $group);
    $field = array(
      'label' => 'Graduation',
      'data_type' => 'Date',
      'html_type' => 'Select Date',
      'custom_group_id' => $createGroup['id'],
    );
    $createField = $this->callAPISuccess('custom_field', 'create', $field);

    $contactParams = $this->fixtures['contact'];
    $contactParams["custom_{$createField['id']}"] = '2013-12-16';
    $contact = $this->callAPISuccess('Contact', 'create', $contactParams);
    $this->_testObjects['CRM_Contact_DAO_Contact'][] = $contact['id'];
    $actionSchedule = $this->fixtures['sched_contact_grad_anniv'];
    $actionSchedule['entity_value'] = "custom_{$createField['id']}";
    $actionScheduleDao = CRM_Core_BAO_ActionSchedule::add($actionSchedule);
    $this->assertTrue(is_numeric($actionScheduleDao->id));
    $this->assertCronRuns(array(
      array(
        // On some random day, no email.
        'time' => '2014-03-07 01:00:00',
        'recipients' => array(),
      ),
      array(
        // A week after their 5th anniversary of graduation, send an email.
        'time' => '2018-12-23 20:00:00',
        'recipients' => array(array('test-member@example.com')),
      ),
    ));
    $this->callAPISuccess('custom_group', 'delete', array('id' => $createGroup['id']));
  }

  // TODO // function testMembershipEndDate_NonMatch() { }
  // TODO // function testEventTypeStartDate_Match() { }
  // TODO // function testEventTypeEndDate_Match() { }
  // TODO // function testEventNameStartDate_Match() { }
  // TODO // function testEventNameEndDate_Match() { }

  /**
   * Run a series of cron jobs and make an assertion about email deliveries.
   *
   * @param array $cronRuns
   *   array specifying when to run cron and what messages to expect; each item is an array with keys:
   *   - time: string, e.g. '2012-06-15 21:00:01'
   *   - recipients: array(array(string)), list of email addresses which should receive messages
   */
  public function assertCronRuns($cronRuns) {
    foreach ($cronRuns as $cronRun) {
      CRM_Utils_Time::setTime($cronRun['time']);
      $this->callAPISuccess('job', 'send_reminder', array());
      $this->mut->assertRecipients($cronRun['recipients']);
      $this->mut->clearMessages();
    }
  }

  /**
   * @var array(DAO_Name => array(int)) List of items to garbage-collect during tearDown
   */
  private $_testObjects;

  /**
   * Sets up the fixture, for example, opens a network connection.
   *
   * This method is called before a test is executed.
   */
  protected function _setUp() {
    $this->_testObjects = array();
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   *
   * This method is called after a test is executed.
   */
  protected function _tearDown() {
    parent::tearDown();
    $this->deleteTestObjects();
  }

  /**
   * This is a wrapper for CRM_Core_DAO::createTestObject which tracks
   * created entities and provides for brainless cleanup.
   *
   * @see CRM_Core_DAO::createTestObject
   *
   * @param $daoName
   * @param array $params
   * @param int $numObjects
   * @param bool $createOnly
   *
   * @return array|NULL|object
   */
  public function createTestObject($daoName, $params = array(), $numObjects = 1, $createOnly = FALSE) {
    $objects = CRM_Core_DAO::createTestObject($daoName, $params, $numObjects, $createOnly);
    if (is_array($objects)) {
      $this->registerTestObjects($objects);
    }
    else {
      $this->registerTestObjects(array($objects));
    }
    return $objects;
  }

  /**
   * @param array $objects
   *   DAO or BAO objects.
   */
  public function registerTestObjects($objects) {
    //if (is_object($objects)) {
    //  $objects = array($objects);
    //}
    foreach ($objects as $object) {
      $daoName = preg_replace('/_BAO_/', '_DAO_', get_class($object));
      $this->_testObjects[$daoName][] = $object->id;
    }
  }

  public function deleteTestObjects() {
    // Note: You might argue that the FK relations between test
    // objects could make this problematic; however, it should
    // behave intuitively as long as we mentally split our
    // test-objects between the "manual/primary records"
    // and the "automatic/secondary records"
    foreach ($this->_testObjects as $daoName => $daoIds) {
      foreach ($daoIds as $daoId) {
        CRM_Core_DAO::deleteTestObjects($daoName, array('id' => $daoId));
      }
    }
    $this->_testObjects = array();
  }

}
