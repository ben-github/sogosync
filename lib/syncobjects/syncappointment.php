<?php
/***********************************************
* File      :   syncappointment.php
* Project   :   Z-Push
* Descr     :   WBXML appointment entities that can be
*               parsed directly (as a stream) from WBXML.
*               It is automatically decoded
*               according to $mapping,
*               and the Sync WBXML mappings.
*
* Created   :   05.09.2011
*
* Copyright 2007 - 2011 Zarafa Deutschland GmbH
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License, version 3,
* as published by the Free Software Foundation with the following additional
* term according to sec. 7:
*
* According to sec. 7 of the GNU Affero General Public License, version 3,
* the terms of the AGPL are supplemented with the following terms:
*
* "Zarafa" is a registered trademark of Zarafa B.V.
* "Z-Push" is a registered trademark of Zarafa Deutschland GmbH
* The licensing of the Program under the AGPL does not imply a trademark license.
* Therefore any rights, title and interest in our trademarks remain entirely with us.
*
* However, if you propagate an unmodified version of the Program you are
* allowed to use the term "Z-Push" to indicate that you distribute the Program.
* Furthermore you may use our trademarks where it is necessary to indicate
* the intended purpose of a product or service provided you use it in accordance
* with honest practices in industrial or commercial matters.
* If you want to propagate modified versions of the Program under the name "Z-Push",
* you may only do so if you have a written permission by Zarafa Deutschland GmbH
* (to acquire a permission please contact Zarafa at trademark@zarafa.com).
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
* Consult LICENSE file for details
************************************************/


class SyncAppointment extends SyncObject {
    public $timezone;
    public $dtstamp;
    public $starttime;
    public $subject;
    public $uid;
    public $organizername;
    public $organizeremail;
    public $location;
    public $endtime;
    public $recurrence;
    public $sensitivity;
    public $busystatus;
    public $alldayevent;
    public $reminder;
    public $rtf;
    public $meetingstatus;
    public $attendees;
    public $body;
    public $bodytruncated;
    public $exception;
    public $deleted;
    public $exceptionstarttime;
    public $categories;



    function SyncAppointment() {
        $mapping = array(
                    SYNC_POOMCAL_TIMEZONE                               => array (  self::STREAMER_VAR      => "timezone",
                                                                                    self::STREAMER_CHECKS   => array(   self::STREAMER_CHECK_REQUIRED       => base64_encode(pack("la64vvvvvvvv"."la64vvvvvvvv"."l",0,"",0,0,0,0,0,0,0,0,0,"",0,0,0,0,0,0,0,0,0)) )),

                    SYNC_POOMCAL_DTSTAMP                                => array (  self::STREAMER_VAR      => "dtstamp",
                                                                                    self::STREAMER_TYPE     => self::STREAMER_TYPE_DATE,
                                                                                    self::STREAMER_CHECKS   => array(   self::STREAMER_CHECK_REQUIRED       => self::STREAMER_CHECK_SETZERO)),

                    SYNC_POOMCAL_STARTTIME                              => array (  self::STREAMER_VAR      => "starttime",
                                                                                    self::STREAMER_TYPE     => self::STREAMER_TYPE_DATE,
                                                                                    self::STREAMER_CHECKS   => array(   self::STREAMER_CHECK_REQUIRED       => self::STREAMER_CHECK_SETZERO,
                                                                                                                        self::STREAMER_CHECK_CMPLOWER       => SYNC_POOMCAL_ENDTIME ) ),


                    SYNC_POOMCAL_SUBJECT                                => array (  self::STREAMER_VAR      => "subject",
                                                                                    self::STREAMER_CHECKS   => array(   self::STREAMER_CHECK_REQUIRED       => self::STREAMER_CHECK_SETEMPTY)),

                    SYNC_POOMCAL_UID                                    => array (  self::STREAMER_VAR      => "uid"),
                    SYNC_POOMCAL_ORGANIZERNAME                          => array (  self::STREAMER_VAR      => "organizername"), // verified below
                    SYNC_POOMCAL_ORGANIZEREMAIL                         => array (  self::STREAMER_VAR      => "organizeremail"), // verified below
                    SYNC_POOMCAL_LOCATION                               => array (  self::STREAMER_VAR      => "location"),
                    SYNC_POOMCAL_ENDTIME                                => array (  self::STREAMER_VAR      => "endtime",
                                                                                    self::STREAMER_TYPE     => self::STREAMER_TYPE_DATE,
                                                                                    self::STREAMER_CHECKS   => array(   self::STREAMER_CHECK_REQUIRED       => self::STREAMER_CHECK_SETONE,
                                                                                                                        self::STREAMER_CHECK_CMPHIGHER      => SYNC_POOMCAL_STARTTIME ) ),

                    SYNC_POOMCAL_RECURRENCE                             => array (  self::STREAMER_VAR      => "recurrence",
                                                                                    self::STREAMER_TYPE     => "SyncRecurrence"),

                    // Sensitivity values
                    // 0 = Normal
                    // 1 = Personal
                    // 2 = Private
                    // 3 = Confident
                    SYNC_POOMCAL_SENSITIVITY                            => array (  self::STREAMER_VAR      => "sensitivity",
                                                                                    self::STREAMER_CHECKS   => array(   self::STREAMER_CHECK_ONEVALUEOF => array(0,1,2,3) )),

                    // Busystatus values
                    // 0 = Free
                    // 1 = Tentative
                    // 2 = Busy
                    // 3 = Out of office
                    SYNC_POOMCAL_BUSYSTATUS                             => array (  self::STREAMER_VAR      => "busystatus",
                                                                                    self::STREAMER_CHECKS   => array(   self::STREAMER_CHECK_REQUIRED   => self::STREAMER_CHECK_SETTWO,
                                                                                                                        self::STREAMER_CHECK_ONEVALUEOF => array(0,1,2,3) )),

                    SYNC_POOMCAL_ALLDAYEVENT                            => array (  self::STREAMER_VAR      => "alldayevent",
                                                                                    self::STREAMER_CHECKS   => array(   self::STREAMER_CHECK_ZEROORONE      => self::STREAMER_CHECK_SETZERO)),

                    SYNC_POOMCAL_REMINDER                               => array (  self::STREAMER_VAR      => "reminder",
                                                                                    self::STREAMER_CHECKS   => array(   self::STREAMER_CHECK_REQUIRED       => self::STREAMER_CHECK_SETZERO,
                                                                                                                        self::STREAMER_CHECK_CMPHIGHER      => -1)),

                    SYNC_POOMCAL_RTF                                    => array (  self::STREAMER_VAR      => "rtf"),

                    // Meetingstatus values
                    //  0 = is not a meeting
                    //  1 = is a meeting
                    //  3 = Meeting received
                    //  5 = Meeting is canceled
                    //  7 = Meeting is canceled and received
                    //  9 = as 1
                    // 11 = as 3
                    // 13 = as 5
                    // 15 = as 7
                    SYNC_POOMCAL_MEETINGSTATUS                          => array (  self::STREAMER_VAR      => "meetingstatus",
                                                                                    self::STREAMER_CHECKS   => array(   self::STREAMER_CHECK_ONEVALUEOF => array(0,1,3,5,7,9,11,13,15) )),

                    SYNC_POOMCAL_ATTENDEES                              => array (  self::STREAMER_VAR      => "attendees",
                                                                                    self::STREAMER_TYPE     => "SyncAttendee",
                                                                                    self::STREAMER_ARRAY    => SYNC_POOMCAL_ATTENDEE),

                    SYNC_POOMCAL_BODY                                   => array (  self::STREAMER_VAR      => "body"),
                    SYNC_POOMCAL_BODYTRUNCATED                          => array (  self::STREAMER_VAR      => "bodytruncated"),
                    SYNC_POOMCAL_EXCEPTIONS                             => array (  self::STREAMER_VAR      => "exceptions",
                                                                                    self::STREAMER_TYPE     => "SyncAppointmentException",
                                                                                    self::STREAMER_ARRAY    => SYNC_POOMCAL_EXCEPTION),

                    SYNC_POOMCAL_CATEGORIES                             => array (  self::STREAMER_VAR      => "categories",
                                                                                    self::STREAMER_ARRAY    => SYNC_POOMCAL_CATEGORY),
                );

        parent::SyncObject($mapping);
    }

    /**
     * Method checks if the object has the minimum of required parameters
     * and fullfills semantic dependencies
     *
     * This overloads the general check() with special checks to be executed
     * Checks if SYNC_POOMCAL_ORGANIZERNAME and SYNC_POOMCAL_ORGANIZEREMAIL are correctly set
     *
     * @access public
     * @return boolean
     */
    public function Check() {
        $ret = parent::check();
        if (!$ret)
            return false;

        // TODO verify if this check is good enough
        if ($this->meetingstatus > 0) {
            if (!isset($this->organizername) || !isset($this->organizeremail)) {
                return false;
            }
        }
        return true;
    }
}

?>