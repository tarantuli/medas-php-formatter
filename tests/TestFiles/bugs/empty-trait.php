<?php

declare(strict_types=1);

namespace Medas\ThisShouldBeNonsense\Functional;

trait AllTestsx
{
    use DefaultValuesTest;
    use ConsoleCommandsTest;
    use DatabaseManagerTest;
    use EntityPersisterTest;
    use EnumTest;
    use HandledPropertyTest;
    use GuidTest;
    use HydratorTest;
    use InheritanceTest;
    use ManyToManyRelationTest;
    use OneToManyRelationTest;
    use PropertyHandlerTest;
    use TimestampsTest;
    use UnsortedTest;
}
