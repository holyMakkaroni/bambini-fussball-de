import fs from 'fs-extra';
import path, { basename } from 'path';
import { parse } from 'vue-docgen-api';
import Handlebars from 'handlebars';

async function generateMarkdownFromComponent(componentPath, template) {
  const componentData = await parse(componentPath, {
    addScriptHandlers: [
      function(
        documentation,
        componentDefinition,
        astPath,
        opt
      ) {
        const leadingComments = componentDefinition.leadingComments;
        if (leadingComments && leadingComments.length) {
          leadingComments.forEach(comment => {
            const commentValue = comment.value || '';
            const descriptionMatch = commentValue.match(/@description\s+(.+)/);
            if (descriptionMatch) {
              documentation.set('description', descriptionMatch[1].trim());
            }
          });
        }
      }
    ],
  });

  const docData = fs.readFileSync(componentPath, 'utf-8');

  const data = {
    name: componentData.displayName,
    description: componentData.description || '',
    props: componentData.props ? componentData.props.map(prop => ({
      name: prop.name,
      type: prop.type ? prop.type.name : 'N/A',
      required: prop.required ? prop.required : false,
      defaultValue: prop.defaultValue ? prop.defaultValue.value.replace(/^'|'$/g, '') : '',
      description: prop.description || false,
    })) : [],
    slots: componentData.slots ? componentData.slots.map(slot => ({
      name: slot.name,
      description: slot.description || 'No description',
    })) : [],
    events: componentData.events ? componentData.events.map(event => ({
      name: event.name,
      description: event.description || 'No description',
    })) : [],
  };

  const compiledMarkdown = template(data);
  return compiledMarkdown;
}

const kebabize = str => {
  return str.split('').map((letter, idx) => {
    return letter.toUpperCase() === letter
      ? `${idx !== 0 ? '-' : ''}${letter.toLowerCase()}`
      : letter;
  }).join('');
}

const camelize = s => s.replace(/-./g, x=>x[1].toUpperCase())

async function processDirectory(sourceDir, outputDir, subfolders, template) {
  const items = await fs.readdir(sourceDir, { withFileTypes: true });

  for (const item of items) {
    const itemPath = path.join(sourceDir, item.name);
    const outputPath = path.join(subfolders, item.name.replace('.vue', '.md'));

    if (item.isDirectory()) {
      // Process subdirectories recursively
      await processDirectory(itemPath, outputDir, subfolders, template);
    } else if (item.isFile() && item.name.endsWith('.vue')) {
      const fileName = (kebabize(basename(outputPath).replace('.md', '').replace('.global', '')) + '.md').replace(/^[^-]*-/, '')

      // Process Vue components
      const markdownContent = await generateMarkdownFromComponent(itemPath, template);
      if (markdownContent) {
        await fs.ensureDir(outputDir);
        await fs.writeFile(outputDir + '/' + fileName, markdownContent, 'utf-8');
      }
    }
  }
}

async function generateMarkdownFiles(sourceDir, outputDir, subfolders, templatePath) {
  const templateContent = await fs.readFile(templatePath, 'utf-8');
  const template = Handlebars.compile(templateContent);

  await processDirectory(sourceDir, outputDir, subfolders, template);
}

export const mkGenerator = (options = {}) => {
  return {
    name: 'vitepress-plugin-nuxt-docgen',
    async buildEnd() {
      const dirs = options.dirs || [];

      for (const { sourceDir, outputDir, templatePath } of dirs) {
        await generateMarkdownFiles(sourceDir, outputDir, outputDir, templatePath);
      }
    }
  };
}