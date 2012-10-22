<?php

/*
 * This file is part of the Sismo utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sismo\iostudio;

use Symfony\Component\Process\Process;

/**
 * Describes a project hosted on iostudio's gitlab server.
 *
 * @author Micah Breedlove <micah.breedlove@iostudio.com>
 */
class GitlabProject extends \Sismo\Project
{
    public function setRepository($url)
    {
        parent::setRepository($url);

        
        if (file_exists($this->getRepository())) {
            $process = new Process('git remote -v', $this->getRepository());
            $process->run();
            foreach (explode("\n", $process->getOutput()) as $line) {
                $parts = explode("\t", $line);
                if ('origin' == $parts[0] && preg_match('#(?:\:|/|@)git.iostudiohq.com(?:\:|/)(.*?)\.git#', $parts[1], $matches)) {
                    $this->setUrlPattern(sprintf('http://git.iostudiohq.com/%s/commit/%%commit%%', $matches[1]));

                    break;
                }

            }
        } elseif (preg_match('#^[a-z0-9_-]+$#i', $this->getRepository())) {
            $this->setUrlPattern(sprintf('http://git.iostudiohq.com/%s/commit/%%commit%%', $this->getRepository()));
            parent::setRepository(sprintf('http://git.iostudiohq.com/%s.git', $this->getRepository()));
        } else {
            throw new \InvalidArgumentException(sprintf('URL "%s" does not look like a iostudio repository.', $this->getRepository()));
        }
    }
}
