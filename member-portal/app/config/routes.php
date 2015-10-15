<?php
MvcRouter::public_connect('{:controller}', array('action' => 'index'));
MvcRouter::public_connect('{:controller}/{:id:[\d]+}', array('action' => 'show'));
MvcRouter::public_connect('{:controller}/{:action}/{:id:[\d]+}');
MvcRouter::public_connect('{:controller}/{:action}');
MvcRouter::public_connect('enroll', array('controller' => 'enrollments', 'action' => 'enroll'));
MvcRouter::public_connect('apply/{:hash}', array('controller' => 'enrollments', 'action' => 'payment'));
MvcRouter::public_connect('renew', array('controller' => 'enrollments', 'action' => 'payment'));
MvcRouter::public_connect('upgrade', array('controller' => 'enrollments', 'action' => 'payment'));

MvcRouter::admin_ajax_connect(array('controller' => 'members', 'action' => 'update_details'));
MvcRouter::admin_ajax_connect(array('controller' => 'members', 'action' => 'update_pwd'));
MvcRouter::admin_ajax_connect(array('controller' => 'members', 'action' => 'update_settings'));
MvcRouter::admin_ajax_connect(array('controller' => 'members', 'action' => 'update_cc'));
MvcRouter::admin_ajax_connect(array('controller' => 'members', 'action' => 'update_auto_renew'));
