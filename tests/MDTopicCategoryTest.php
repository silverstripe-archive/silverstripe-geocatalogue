<?php

class MDTopicCategoryTest extends SapphireTest
{
    
    /**
    *  check getTopicCategoryNice
    */
    public function testgetTopicCategoryNice()
    {
        $topic  = new MDTopicCategory();
        $topic->Value = 'biota';

        //check initial value
        $this->assertEquals($topic->Value, 'biota', 'initial data');
        
        // make it nice
        $topicName=$topic->getTopicCategoryNice();
        $this->assertEquals($topicName, 'Biota', 'on initial data');
        
        // checking empty value
        $topic->Value='';
        $topicName=$topic->getTopicCategoryNice();
        $this->assertEquals($topicName, '', 'on empty value');
        
        //null-Value should return the default for null
        $topic->Value=null;
        $topicName=$topic->getTopicCategoryNice();
        $this->assertEquals($topicName, '', 'on null value');
        
        //checking an invalid value
        $topic->Value='INVALID_TOPIC';
        $topicName=$topic->getTopicCategoryNice();
        $this->assertEquals($topicName, '', 'on invalid value');
    }
}
