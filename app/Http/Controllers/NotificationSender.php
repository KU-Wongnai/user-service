<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RabbitMQPublisher;

class NotificationSender extends Controller
{
    // Email part
    public function sendEmailWelcomeUser(String $email)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $email, 'type' => 'email.welcome.user' ]), 'events.notification', 'email.welcome');
        
        return "Email Welcome User sent";
    }

    public function sendEmailWelcomeRestaurant(String $email)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $email, 'type' => 'email.welcome.restaurant' ]), 'events.notification', 'email.welcome');
        
        return "Email Welcome Restaurant sent";
    }

    public function sendEmailWelcomeRider(String $email)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $email, 'type' => 'email.welcome.rider' ]), 'events.notification', 'email.welcome');
        
        return "Email Welcome Rider sent";
    }


    // In-app notification part
    public function sendInAppWelcomeNewUser(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.WelcomeNewUser' ]), 'events.notification', 'email.welcome');
        return "InApp Welcome User sent";
    }

    public function sendInAppUserReviewHasReplied(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.UserReviewHasReplied' ]), 'events.notification', 'email.welcome');
        return "InApp UserReviewHasReplied sent";
    }

    public function sendInAppUserReviewNewFollower(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.UserReviewNewFollower' ]), 'events.notification', 'email.welcome');
        return "InApp UserReviewNewFollower sent";
    }

    public function sendInAppUserReviewWeekly(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.UserReviewWeekly' ]), 'events.notification', 'email.welcome');
        return "InApp UserReviewWeekly sent";
    }

    public function sendInAppThankUser(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.ThankUser' ]), 'events.notification', 'email.welcome');
        return "InApp ThankUser sent";
    }

    public function sendInAppUserDeliveryOrder(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.UserDeliveryOrder' ]), 'events.notification', 'email.welcome');
        return "InApp UserDeliveryOrder sent";
    }

    public function sendInAppUserDeliveryWait(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.UserDeliveryWait' ]), 'events.notification', 'email.welcome');
        return "InApp UserDeliveryWait sent";
    }

    public function sendInAppUserDeliveryFinished(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.UserDeliveryFinished' ]), 'events.notification', 'email.welcome');
        return "InApp UserDeliveryFinished sent";
    }

    public function sendInAppUserDeliveryRemindReview(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.UserDeliveryRemindReview' ]), 'events.notification', 'email.welcome');
        return "InApp UserDeliveryRemindReview sent";
    }

    public function sendInAppUserDeliveryReorder(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.UserDeliveryReorder' ]), 'events.notification', 'email.welcome');
        return "InApp UserDeliveryReorder sent";
    }

    public function sendInAppWelcomeNewRider(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.WelcomeNewRider' ]), 'events.notification', 'email.welcome');
        return "InApp WelcomeNewRider sent";
    }

    public function sendInAppRiderNewOrder(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.RiderNewOrder' ]), 'events.notification', 'email.welcome');
        return "InApp RiderNewOrder sent";
    }

    public function sendInAppRiderOrderOnWay(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.RiderOrderOnWay' ]), 'events.notification', 'email.welcome');
        return "InApp RiderOrderOnWay sent";
    }

    public function sendInAppRiderOrderFinished(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.RiderOrderFinished' ]), 'events.notification', 'email.welcome');
        return "InApp RiderOrderFinished sent";
    }

    public function sendInAppRiderSafetyReminder(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.RiderSafetyReminder' ]), 'events.notification', 'email.welcome');
        return "InApp RiderSafetyReminder sent";
    }

    public function sendInAppThankRider(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.ThankRider' ]), 'events.notification', 'email.welcome');
        return "InApp ThankRider sent";
    }

    public function sendInAppWelcomeNewRestaurant(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.WelcomeNewRestaurant' ]), 'events.notification', 'email.welcome');
        return "InApp WelcomeNewRestaurant sent";
    }

    public function sendInAppRestaurantHasReview(String $id)
    {
        $publisher = new RabbitMQPublisher();
        $publisher->declareExchange('events.notification', 'topic');
        $publisher->publish(json_encode([ 'to' => $id, 'type' => 'noti.RestaurantHasReview' ]), 'events.notification', 'email.welcome');
        return "InApp RestaurantHasReview sent";
    }


}
